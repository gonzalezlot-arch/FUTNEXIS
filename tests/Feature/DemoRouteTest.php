<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DemoRouteTest extends TestCase
{
    public function test_renders_the_demo_in_the_local_environment(): void
    {
        $this->app['env'] = 'local';

        $this->get(route('internal.demo'))
            ->assertOk()
            ->assertSee('DATOS FICTICIOS');
    }

    #[TestWith(['production'])]
    #[TestWith(['staging'])]
    #[TestWith(['testing'])]
    public function test_responds_not_found_outside_the_local_environment(string $environment): void
    {
        $this->app['env'] = $environment;

        $this->get(route('internal.demo'))->assertNotFound();
    }
}
