<?php

namespace Tests\Unit\Services\MovieSearch\Models;

use App\Services\MovieSearch\Models\SearchMeta;
use Tests\TestCase;

class SearchMetaTest extends TestCase {

    public function test_set_and_get_total(): void {
        $meta = new SearchMeta();
        
        $result = $meta->setTotal(100);
        
        $this->assertSame($meta, $result);
        $this->assertEquals(100, $meta->getTotal());
    }

    public function test_default_total_is_zero(): void {
        $meta = new SearchMeta();
        
        $this->assertEquals(0, $meta->getTotal());
    }

    public function test_set_and_get_per_page(): void {
        $meta = new SearchMeta();
        
        $result = $meta->setPerPage(10);
        
        $this->assertSame($meta, $result);
        $this->assertEquals(10, $meta->getPerPage());
    }

    public function test_set_and_get_pages(): void {
        $meta = new SearchMeta();
        
        $result = $meta->setPages(5);
        
        $this->assertSame($meta, $result);
        $this->assertEquals(5, $meta->getPages());
    }

    public function test_set_and_get_current_page(): void {
        $meta = new SearchMeta();
        
        $result = $meta->setCurrentPage(2);
        
        $this->assertSame($meta, $result);
        $this->assertEquals(2, $meta->getCurrentPage());
    }

    public function test_to_array(): void {
        $meta = new SearchMeta();
        $meta->setTotal(50)
            ->setPerPage(10)
            ->setPages(5)
            ->setCurrentPage(3);
        
        $expected = [
            'total' => 50,
            'perPage' => 10,
            'pages' => 5,
            'currentPage' => 3,
        ];
        
        $this->assertEquals($expected, $meta->toArray());
    }

    public function test_fluent_interface_chaining(): void {
        $meta = new SearchMeta();
        
        $result = $meta
            ->setTotal(200)
            ->setPerPage(20)
            ->setPages(10)
            ->setCurrentPage(1);
        
        $this->assertSame($meta, $result);
    }
}
