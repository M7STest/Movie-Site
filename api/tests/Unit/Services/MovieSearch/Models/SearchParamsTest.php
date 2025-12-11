<?php

namespace Tests\Unit\Services\MovieSearch\Models;

use App\Services\MovieSearch\Models\SearchParams;
use Tests\TestCase;

class SearchParamsTest extends TestCase {

    public function test_set_and_get_title(): void {
        $params = new SearchParams();
        
        $result = $params->setTitle('Batman');
        
        $this->assertSame($params, $result); // Fluent interface
        $this->assertEquals('Batman', $params->getTitle());
    }

    public function test_default_title_is_empty_string(): void {
        $params = new SearchParams();
        
        $this->assertEquals('', $params->getTitle());
    }

    public function test_set_and_get_type(): void {
        $params = new SearchParams();
        
        $result = $params->setType('movie');
        
        $this->assertSame($params, $result);
        $this->assertEquals('movie', $params->getType());
    }

    public function test_has_type_returns_false_when_null(): void {
        $params = new SearchParams();
        
        $this->assertFalse($params->hasType());
    }

    public function test_has_type_returns_true_when_set(): void {
        $params = new SearchParams();
        $params->setType('series');
        
        $this->assertTrue($params->hasType());
    }

    public function test_set_and_get_year(): void {
        $params = new SearchParams();
        
        $result = $params->setYear(2024);
        
        $this->assertSame($params, $result);
        $this->assertEquals(2024, $params->getYear());
    }

    public function test_has_year_returns_false_when_null(): void {
        $params = new SearchParams();
        
        $this->assertFalse($params->hasYear());
    }

    public function test_has_year_returns_true_when_set(): void {
        $params = new SearchParams();
        $params->setYear(2020);
        
        $this->assertTrue($params->hasYear());
    }

    public function test_set_and_get_page(): void {
        $params = new SearchParams();
        
        $result = $params->setPage(3);
        
        $this->assertSame($params, $result);
        $this->assertEquals(3, $params->getPage());
    }

    public function test_default_page_is_one(): void {
        $params = new SearchParams();
        
        $this->assertEquals(1, $params->getPage());
    }

    public function test_fluent_interface_chaining(): void {
        $params = new SearchParams();
        
        $result = $params
            ->setTitle('Inception')
            ->setType('movie')
            ->setYear(2010)
            ->setPage(2);
        
        $this->assertSame($params, $result);
        $this->assertEquals('Inception', $params->getTitle());
        $this->assertEquals('movie', $params->getType());
        $this->assertEquals(2010, $params->getYear());
        $this->assertEquals(2, $params->getPage());
    }
}
