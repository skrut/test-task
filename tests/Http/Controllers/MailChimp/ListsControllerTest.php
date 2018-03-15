<?php
declare(strict_types=1);

namespace Tests\App\Http\Controllers\MailChimp;

use Tests\App\TestCases\MailChimp\ListTestCase;

class ListsControllerTest extends ListTestCase
{
    public function testCreateListSuccessfully(): void
    {
        $this->post('/mailchimp/lists', static::$listData);

        $this->assertResponseOk();
    }
}
