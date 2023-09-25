<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnimalTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testViewAllAnimal()
    {
        //請求 api/animal 結果存入 $response
        $response = $this->get('api/animals');

        //assertJsonStructure 判斷 Json 結構是否與我們下方的結構相同
        $response->assertJsonStructure([
            "current_page",
            "data" => [
                [
                    "id",
                    "type_id",
                    "name",
                    "birthday",
                    "area",
                    "fix",
                    "description",
                    "personality",
                    "created_at",
                    "updated_at",
                    "user_id"
                ]
            ],
            "first_page_url",
            "from",
            "last_page",
            "last_page_url",
            "next_page_url",
            "path",
            "per_page",
            "prev_page_url",
            "to",
            "total",
        ]);
    }

    /**
    * 測試建立animal
    *
    * @return void
    */
    public function testCanCreateAnimal()
    {
        Passport::actingAs(
            User::first(),
            ['*']
        );

        // 請求時並傳入資料
        $response = $this->json(
            'POST', 'api/animals',
            [
                'type_id' => '1',
                'name' => '大黑',
                'birthday' => '2019-10-05', //今天要補班
                'area' => '台北市',
                'fix' => '1'
            ]
        );

        //檢查返回資料
        $response->assertStatus(201)  //狀態碼應屬於201
            ->assertJson(
                [
                    "type_id"=> "1",
                    "name"=>"大黑",
                    "birthday"=>"2019-10-05",
                    "area"=>"台北市",
                    "fix"=>"1"
                ]
            );
    }

    /**
     * 測試不能建立animal
     *
     * @return void
     */
    public function testCanNotCreateAnimal()
    {
        // 沒有模擬會員權限的程式

        // 請求時並傳入資料
        $response = $this->json(
            'POST', 'api/animals',
            [
                'type_id' => '1',
                'name' => '大黑',
                'birthday' => '2019-10-05', //今天要補班
                'area' => '台北市',
                'fix' => '1'
            ]
        );

        //檢查返回資料
        $response->assertStatus(401)  //沒有token，狀態碼應屬於401
            ->assertJson(
                [
                    "message" => "Unauthenticated."
                ]
            );
    }
}
