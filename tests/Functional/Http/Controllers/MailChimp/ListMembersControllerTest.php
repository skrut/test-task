<?php
declare(strict_types=1);

namespace tests\App\Functional\Http\Controllers\MailChimp;

use Tests\App\TestCases\MailChimp\ListMemberTestCase;
use Tests\App\TestCases\MailChimp\MailChimpData;

/**
 * Class ListMembersControllerTest
 * @package tests\App\Functional\Http\Controllers\MailChimp
 */
class ListMembersControllerTest extends ListMemberTestCase
{
    /**
     * Test application add successfully member to list and returns it back with id from MailChimp.
     *
     * @return void
     */
    public function testCreateListMemberSuccessfully(): void
    {
        // create list
        $this->createListMemberViaApi(MailChimpData::$listData);

        // create list member
        $this->post("/mailchimp/lists/{$this->listId}/members", MailChimpData::$listMemberData);

        $content = \json_decode($this->response->getContent(), true);

        $this->assertResponseOk();
        $this->seeJson(MailChimpData::$listMemberData);

        self::assertArrayHasKey('mail_chimp_id', $content);
        self::assertNotNull($content['mail_chimp_id']);
    }

    /**
     * Test application returns successful response with list member data when requesting existing list.
     *
     * @return void
     */
    public function testShowListMemberSuccessfully(): void
    {
        $listMember = $this->createListMember(MailChimpData::$listData, MailChimpData::$listMemberData);

        $this->get(\sprintf('/mailchimp/lists/%s/members/%s', $listMember->getMailChimpList()->getId(), $listMember->getId()));
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (MailChimpData::$listMemberData as $key => $value) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals($value, $content[$key]);
        }
    }


    /**
     * Test application returns successfully response when updating existing list member with updated values.
     *
     * @return void
     */
    public function testUpdateListSuccessfully(): void
    {
        // create list
        $this->createListMemberViaApi(MailChimpData::$listData);

        // create list member
        $this->post("/mailchimp/lists/{$this->listId}/members/", MailChimpData::$listMemberData);
        $listMember = \json_decode($this->response->content(), true);

        // update list member
        $this->put(\sprintf('/mailchimp/lists/%s/members/%s', $this->listId , $listMember['list_member_id']), ['language' => 'ru']);
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (\array_keys(MailChimpData::$listMemberData) as $key) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals('ru', $content['language']);
        }
    }

    /**
     * Test application returns empty successful response when removing existing list.
     *
     * @return void
     */
    public function testRemoveMemberFromListSuccessfully(): void
    {
        // create list
        $this->createListMemberViaApi(MailChimpData::$listData);

        // create list member
        $this->post("/mailchimp/lists/{$this->listId}/members/", MailChimpData::$listMemberData);
        $listMember = \json_decode($this->response->content(), true);

        // remove member from list
        $this->delete(\sprintf('/mailchimp/lists/%s/members/%s', $this->listId, $listMember['list_member_id']));

        $this->assertResponseOk();
        self::assertEmpty(\json_decode($this->response->content(), true));
    }
}
