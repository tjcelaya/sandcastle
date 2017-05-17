<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 4/20/17
 * Time: 1:23 PM
 */

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Base;
use App\Model\EloquentRelatable;
use App\Model\Issue;
use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CrudController extends Controller
{
    const OPS = ['=', '<=', '<', '>=', '>', '<>', 'like'];

    const MODELS = [
        'issue' => \App\Model\Issue::class,
        'contact' => \App\Model\Contact::class,
    ];

    /** @var */
    protected $serializer;

    /**
     * @var Repository
     */
    private $cache;

    public function __construct(Repository $cache)
    {
        // $this->serializer = new HalJsonSerializer(new HalJsonTransformer($mapper));
        // $f = app('Dingo\Api\Transformer\Factory')->register(Issue::class, 'App\Transformer\Base');
        $this->cache = $cache;
    }

    protected function shouldLoadRelation($linksRequested, $name, $relationSpec)
    {
        return (
            ($autoloaded = (isset($relationSpec['autoload']) && $relationSpec['autoload']))
            ||
            ($stringRequested = ($linksRequested == 'all' || $linksRequested == $name))
            ||
            ($arrayRequested = (is_array($linksRequested) && in_array($name, $linksRequested)))
        );
    }

    protected function naiveRouteFromClass($klass)
    {
        return snake_case(last(explode('\\', $klass)));
    }

    public function index(Request $request)
    {
        // \Log::info('resource owner'.\Authorizer::getResourceOwnerId());
        $klass = $this->extractModelFromPath($request);
        $relationships = (new $klass)->getAvailableRelations();

        /** @var \Illuminate\Database\Eloquent\Builder $thingsQ */
        $thingsQ = $klass::query();

        if ($request->has('filter')) {
            $thingsQ = $this->processFilter($thingsQ, $request->input('filter'));
        }
        if ($request->has('sort')) {
            $thingsQ = $this->processSort($thingsQ, $request->input('sort'));
        }
        if ($request->has('mscope')) {
            $thingsQ = $this->processMscope($thingsQ, $request->input('mscope'));
        }

        $things = $thingsQ->paginate();

        $linksRequested = $request->input('links');
        foreach ($relationships as $name => $relationSpec) {
            if ($this->shouldLoadRelation($linksRequested, $name, $relationSpec)) {
                $things->load($name);
            }
        }

        return $things;
        // $transformer = $this->serializer->getTransformer();
        // $transformer->setSelfUrl(route($this->naiveRouteFromClass($klass) . '.index'));
        // return $this->response($this->serializer->serialize($things));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Base|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $instance = $this->extractModelFromPath($request);
        \Log::debug($klass, $request->input());

        DB::beginTransaction();

        /** @var \App\Model\Base $thing */
        $thing = new $klass($request->input());
        $thing->save();

        try {
            $relations = $this->attachRelations($thing, $request);
        } catch (Exception $e) {
            \Log::error($e);
            DB::rollBack();
            abort(400, 'Failed to attach related data');
        }

        DB::commit();
        logn();

        if (class_exists($klassCreated = 'App\Events\\' . studly_case($klass) . 'Created')) {
            event(new $klassCreated($thing));
        }

        return $thing;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        logy();
        $klass = $this->extractModelFromPath($request);
        /** @var EloquentRelatable $thing */
        $thing = $klass::find($id);

        if (!$thing) {
            abort(404, last(explode('\\', $klass)) . ' not found');
        }

        $linksRequested = $request->get('links');
        foreach ($thing->getAvailableRelations() as $name => $relationSpec) {
            if ($this->shouldLoadRelation($linksRequested, $name, $relationSpec)) {
                $thing->load($name);
            }
        }
        logn();

        return $thing;
        // $transformer = $this->serializer->getTransformer();
        // $transformer->setSelfUrl(route($this->naiveRouteFromClass($klass) . '.show', ['id' => $id]));
        // return $this->response($this->serializer->serialize($thing));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $klass = $this->extractModelFromPath($request);
        $thing = $klass::findOrFail($id);
        $input = $request->input();
        $thing->update(isset($input['_embedded']) ? $this->extractEmbedded($input) : $input);

        return $thing;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $klass = $this->extractModelFromPath($request);
        $thing = $klass::findOrFail($id);
        $thing->delete();

        return $thing;
    }

    public function options(Request $request)
    {
        $klass = $this->extractModelFromPath($request);

        if ($cache && $cache->has($cacheKey = $klass . ':options')) {
            \Log::info("found $cacheKey in cache");
            return $cache->get($cacheKey);
        }

        $fields = $this->generateJsonSchemaFromBaseModel($request, $klass);

        if ($cache) {
            $cacheKey = $klass . ':options';
            \Log::info("putting $cacheKey in cache");
            $cache->put($cacheKey, $fields, 60);
        }

        return $fields;
    }

    protected function attachRelations($thing, Request $request)
    {

        $klass = $this->extractModelFromPath($request);
        $relationships = (new $klass)->getAvailableRelations();

        // array_intersect_key takes the key,value pairs from the first array
        // that have keys present in all other arguments provided.
        // quickly.

        $matchingKeys = array_intersect_key($request->input(), $relationships);

        foreach ($matchingKeys as $name => $relationData) {
            if (!isset($relationships[$name])) {
                \Log::info(
                    'exiting early because ' . $name . ' is not in ' . var_export(
                        array_keys($relationships),
                        true
                    )
                );
                continue;
            }

            \Log::debug('saving relations of this kind of thing -> ', $relationships[$name]);

            if ($relationships[$name]['cardinality'] == 'many') {
                $relatedThings = new Collection;

                \Log::info('many ' . var_export($relationData, true));

                foreach ($relationData as $relatedThingData) {
                    \Log::info(
                        'making a ' .
                        $relationships[$name]['class'] .
                        ' from this data: ' .
                        json_encode($relatedThingData)
                    );
                    $relatedKlass = $relationships[$name]['class'];
                    /** @var \App\Model\Base $relatedThing */
                    $relatedThing = new $relatedKlass($relatedThingData);
                    $relatedThing->save();
                    $relatedThings[] = $relatedThing;
                }

                \Log::info('going to save some ' . $thing->$name . var_export($relationData, true));

                $attachResult = $thing->{$name}()->saveMany($relatedThings);
                $thing->load($name);
            } else {
                throw new Exception(
                    'Unexpected cardinality: ' .
                    $relationships[$name]['cardinality'] .
                    ' for relationship ' .
                    $klass .
                    ' -> ' .
                    $relationships[$name]['class']
                );
            }
        }
    }

    private function extractEmbedded(&$input)
    {
        if (empty(($input['_embedded'])) || !is_array($input['_embedded'])) {
            return;
        }

        foreach ($input['_embedded'] as $ek => $eV) {
            $input[$ek] = $eV;
        }

        unset($input['_embedded']);

        return $input;
    }

    private function addSchemaForObject(EloquentRelatable $instance, &$fields, $nested = false)
    {
        $fields['type'] = 'object';

        foreach ($instance->getVisible() as $prop) {
            $fields['properties'][$prop] = [
                'name' => $prop,
                'description' => title_case($prop), // TODO: get these from somewhere
                'type' => gettype($instance->{$prop}),
            ];
        }

        $klass = get_class($instance);
        foreach ($instance->getAvailableRelations() as $name => $relationSpec) {
            $relatedKlass = $relationSpec['class'];
            $rM = factory($relatedKlass)->make(); // TODO: figure out how to do this without DB call

            $fields['properties'][$name] = [
                'type' => $relationSpec['cardinality'] == 'many' ? 'array' : 'object',
                //                array_flip($rM->getFillable()),
            ];

            if ($relationSpec['cardinality'] == 'many') {
                $this->addSchemaForObject($rM, $fields['properties'][$name]['items']);
            } else {
                $this->addSchemaForObject($rM, $fields['properties'][$name]);
            }
        }
    }

    /**
     * Process $input that looks like ?filter[id]=1
     *
     * @param Builder $thingsQ
     * @param array $input
     *
     * @return Builder
     */
    private function processFilter(Builder $thingsQ, array $input)
    {
        foreach ($input as $fieldKey => $fieldCompare) {
            // list($thing, $relatedThings) = explode('.', $fieldKey);

            if (mb_strpos($fieldCompare, '.', 0, 'UTF-8')) {
                abort(400, 'No nested property access!');
            }

            if (mb_strpos($fieldCompare, ':', 0, 'UTF-8')) {
                list($op, $val) = explode(':', $fieldCompare, 2);
            } else {
                $op = '=';
                $val = $fieldCompare;
            }


            if (empty($op)) {
                // filter[$fK]=$val
                $this->where($fieldKey, $op);
            } elseif (in_array(strtolower($op), self::OPS) && mb_strlen($val)) {
                // filter[$fK]=$op:$val
                $thingsQ->where($fieldKey, $op, $val);
            } elseif ($op === 'in') {
                if (strpos($val, ',', 0)
                    && ($vals = explode(',', $val))
                ) {
                    // filter[$fK]=in:1,2,3
                    $thingsQ->whereIn('id', $vals);
                } else {
                    // filter[$fK]=in:1
                    $thingsQ->where('id', $val);
                }
            } elseif ($op === 'has') {
                if (strpos($val, ',', 0)
                    && ($vals = explode(':', $val, 2))
                ) {
                    list($comparisonOperator, $count) = $vals;
                    // filter[$fK]=has:>=:1
                    $thingsQ->has($fieldKey, $comparisonOperator ?: '>=', $count ?: 1);
                    null;
                } else {
                    // filter[$fK]=has
                    $thingsQ->has($fieldKey);
                }
            } elseif ($op === 'isnotnull') {
                // filter[$fK]=isnotnull
                $thingsQ->whereNotNull($fieldKey);
            } elseif ($op === 'isnull') {
                // filter[$fK]=isnull
                $thingsQ->whereNull($fieldKey);
            }
        }

        return $thingsQ;
    }

    /**
     *
     * http://jsonapi.org/format/#fetching-sorting
     * An endpoint MAY support requests to sort the primary data with a sort query parameter. The value for sort MUST
     * represent sort fields. An endpoint MAY support multiple sort fields by allowing comma-separated (U+002C COMMA,
     * “,”) sort fields. Sort fields SHOULD be applied in the order specified. The sort order for each sort field MUST
     * be ascending unless it is prefixed with a minus (U+002D HYPHEN-MINUS, “-“), in which case it MUST be descending.
     *
     * @param Builder $thingsQ
     * @param         $input
     *
     * @return Builder
     */
    private function processSort(Builder $thingsQ, $input)
    {
        foreach (explode(',', $input) as $field) {
            $dir = 'ASC';
            if (mb_strpos($field, '-') !== false) {
                $field = substr($field, 1);
                $dir = 'DESC';
            }
            $thingsQ->orderBy($field, $dir);
        }

        return $thingsQ;
    }

    private function processMscope($thingsQ, $input)
    {
        // call scope functions after some validation maybe?
        return $thingsQ;
    }

    /**
     * @param Request $request
     * @param $klass
     * @return array
     */
    public function generateJsonSchemaFromBaseModel(Request $request, $klass): array
    {
        $fields = [];

        /** @var EloquentRelatable $m */
        $m = factory($klass)->make();
        $this->addSchemaForObject($m, $fields);

        $fields['title'] = studly_case($this->naiveRouteFromClass($klass));
        $fields['path'] = $request->path();
        return $fields;
    }

    protected function extractModelFromPath(Request $request): string
    {
        $klass = array_get($request->segments(), 1);

        $allowed_crud_resource = isset(self::MODELS[$klass]);
        $instantiable = class_exists(self::MODELS[$klass]);
        if (!$allowed_crud_resource || !$instantiable) {
            abort(500);
        }

        return self::MODELS[$klass];
    }
}
