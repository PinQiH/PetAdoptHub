<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Animal;
use App\Http\Controllers\Controller;
use App\Services\AnimalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\AnimalResource;
use App\Http\Requests\StoreAnimalRequest;

class AnimalController extends Controller
{
    private $animalService;

    public function __construct(AnimalService $animalService)
    {
        $this->animalService = $animalService;
        $this->middleware('auth:api', ['except' => ['index','show']]);
    }

    /**
     * Show Animal List
     *
     * @queryParam marker 標記由哪個資源開始查詢(預設ID:1) Example: 1
     * @queryParam limit  設定回傳資料筆數(預設10筆資料) Example: 10
     * @queryParam sort   設定排序方式 Example: name:asc
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 設定預設值

        $marker = isset($request->marker) ? $request->marker : 1;
        $limit = isset($request->limit) ? $request->limit : 10;

        $query = Animal::query();

        // 篩選欄位條件
        // if (isset($request->filters)) {
        //     $filters = explode(',', $request->filters);
        //     foreach ($filters as $key => $filter) {
        //         list($criteria, $value) = explode(':', $filter);
        //         $query->where($criteria, $value);
        //     }
        // }
        $query = $this->animalService->filterAnimals($request->filters, $query);

        //排列順序
        // if (isset($request->sort)) {
        //     $sorts = explode(',', $request->sort);
        //     foreach ($sorts as $key => $sort) {
        //         list($criteria, $value) = explode(':', $sort);
        //         if ($value == 'asc' || $value == 'desc') {
        //             $query->orderBy($criteria, $value);
        //         }
        //     }
        // } else {
        //     $query->orderBy('id', 'asc');
        // }
        $query = $this->animalService->sortAnimals($request->sort, $query);

        $animals = $query->where('id', '>=', $marker)->paginate($limit);

        return response($animals, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store Animal
     *
     * @authenticated
     *
     * @bodyParam type_id Int required 動物的分類ID(需參照types資料表) Example: 1
     * @bodyParam name String required 動物名稱 Example: 黑熊
     * @bodyParam birthday date required 生日 Example: 2019-10-10
     * @bodyParam area String required 所在區域 Example: 台北市
     * @bodyParam fix boolean required 是否結紮 Example: true
     * @bodyParam description text 簡易描述 Example: 黑狗，胸前有白毛！宛如台灣黑熊
     * @bodyParam personality text 其他介紹 Example: 非常親人！很可愛～
     *
     *
     * @param  App\Http\Requests\StoreAnimalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnimalRequest $request)
    {
        // $this->validate($request, [
        //     'type_id' => 'required',
        //     'name' => 'required|max:255',
        //     'birthday' => 'required|date',
        //     'area' => 'required|max:255',
        //     'fix' => 'required|boolean',
        //     'description' => 'nullable',
        //     'personality' => 'nullable'
        // ]);

        //Animal Model 有 create 寫好的方法，把請求的內容，用all方法轉為陣列，傳入 create 方法中。
        $animal = Animal::create($request->all());

        // 回傳 animal 產生出來的實體物件資料，第二個參數設定狀態碼，可以直接寫 201 表示創建成功的狀態螞或用下面 Response 功能
        return response($animal, Response::HTTP_CREATED);
    }

    /**
     * Show Animal
     *
     * @param  \App\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function show(Animal $animal)
    {
        return response(new AnimalResource($animal), Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function edit(Animal $animal)
    {
        //
    }

    /**
     * Update Animal
     *
     * @bodyParam type_id Int required 動物的分類ID(需參照types資料表) Example: 1
     * @bodyParam name String required 動物名稱 Example: 黑熊
     * @bodyParam birthday date required 生日 Example: 2019-10-10
     * @bodyParam area String required 所在區域 Example: 台北市
     * @bodyParam fix boolean required 是否結紮 Example: true
     * @bodyParam description text 簡易描述 Example: 黑狗，胸前有白毛！宛如台灣黑熊
     * @bodyParam personality text 其他介紹 Example: 非常親人！很可愛～
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Animal $animal)
    {
        $this->authorize('update', $animal);
        $animal->update($request->all());
        return response($animal, Response::HTTP_OK);
    }

    /**
     * Delete Animal
     *｀
     * @authenticated
     *
     * @param  \App\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    // 路由有設定 animal 變數，這裡設定它是 Animal 模型，所以會自動找出該ID的實體物件
    public function destroy(Animal $animal)
    {
        // 把這個實體物件刪除
        $animal->delete();
        // 回傳 null 並且給予 204 狀態碼
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Like/Unlike Animal
     *
     * @authenticated
     *
     * @param  \App\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function like(Animal $animal)
    {
        $animal->like()->toggle(Auth::user()->id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
