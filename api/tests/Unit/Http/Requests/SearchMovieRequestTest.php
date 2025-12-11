<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\SearchMovieRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SearchMovieRequestTest extends TestCase {

    private SearchMovieRequest $request;

    protected function setUp(): void {
        parent::setUp();
        
        $this->request = new SearchMovieRequest();
    }

    public function test_authorize_returns_true(): void {
        $this->assertTrue($this->request->authorize());
    }

    public function test_title_is_required(): void {
        $validator = Validator::make(
            ['type' => 'movie'],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_title_must_be_string(): void {
        $validator = Validator::make(
            ['title' => 123],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_title_max_length_is_255(): void {
        $validator = Validator::make(
            ['title' => str_repeat('a', 256)],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_valid_title_passes(): void {
        $validator = Validator::make(
            ['title' => 'Batman'],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_type_is_optional(): void {
        $validator = Validator::make(
            ['title' => 'Batman'],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_type_must_be_valid_value(): void {
        $validator = Validator::make(
            ['title' => 'Batman', 'type' => 'invalid'],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
    }

    public function test_type_accepts_movie(): void {
        $validator = Validator::make(
            ['title' => 'Batman', 'type' => 'movie'],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_type_accepts_series(): void {
        $validator = Validator::make(
            ['title' => 'Breaking Bad', 'type' => 'series'],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_type_accepts_episode(): void {
        $validator = Validator::make(
            ['title' => 'Episode Title', 'type' => 'episode'],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_year_is_optional(): void {
        $validator = Validator::make(
            ['title' => 'Batman'],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_year_must_be_integer(): void {
        $validator = Validator::make(
            ['title' => 'Batman', 'year' => 'not-a-year'],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('year', $validator->errors()->toArray());
    }

    public function test_year_minimum_is_1800(): void {
        $validator = Validator::make(
            ['title' => 'Batman', 'year' => 1799],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('year', $validator->errors()->toArray());
    }

    public function test_year_maximum_is_current_year(): void {
        $currentYear = (int)date('Y');
        
        $validator = Validator::make(
            ['title' => 'Batman', 'year' => $currentYear + 1],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('year', $validator->errors()->toArray());
    }

    public function test_valid_year_passes(): void {
        $validator = Validator::make(
            ['title' => 'Batman', 'year' => 2008],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_page_is_optional(): void {
        $validator = Validator::make(
            ['title' => 'Batman'],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_page_must_be_integer(): void {
        $validator = Validator::make(
            ['title' => 'Batman', 'page' => 'not-a-number'],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('page', $validator->errors()->toArray());
    }

    public function test_page_minimum_is_1(): void {
        $validator = Validator::make(
            ['title' => 'Batman', 'page' => 0],
            $this->request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('page', $validator->errors()->toArray());
    }

    public function test_valid_page_passes(): void {
        $validator = Validator::make(
            ['title' => 'Batman', 'page' => 5],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_all_fields_together_pass(): void {
        $currentYear = (int)date('Y');
        
        $validator = Validator::make(
            [
                'title' => 'The Dark Knight',
                'type' => 'movie',
                'year' => 2008,
                'page' => 1,
            ],
            $this->request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_custom_error_messages_exist(): void {
        $messages = $this->request->messages();

        $this->assertIsArray($messages);
        $this->assertArrayHasKey('title.required', $messages);
        $this->assertArrayHasKey('type.in', $messages);
        $this->assertArrayHasKey('year.min', $messages);
        $this->assertArrayHasKey('page.min', $messages);
    }

    public function test_title_required_custom_message(): void {
        $messages = $this->request->messages();
        
        $this->assertEquals('The title field is required.', $messages['title.required']);
    }

    public function test_type_in_custom_message(): void {
        $messages = $this->request->messages();
        
        $this->assertStringContainsString('movie, series, episode', $messages['type.in']);
    }
}
