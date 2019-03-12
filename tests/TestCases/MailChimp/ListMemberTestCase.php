<?php
declare(strict_types=1);

namespace Tests\App\TestCases\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpListMember;
use Mailchimp\Mailchimp;
use Tests\App\TestCases\WithDatabaseTestCase;

abstract class ListMemberTestCase extends WithDatabaseTestCase
{
    /** @var string */
    protected $listId;

    /** @var string */
    protected $listMailChimpId;

    /**
     * Call MailChimp to delete lists created during test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        if ($this->listMailChimpId) {
            /** @var Mailchimp $mailChimp */
            $mailChimp = $this->app->make(Mailchimp::class);

            $mailChimp->delete(\sprintf('lists/%s', $this->listMailChimpId));
        }

        parent::tearDown();
    }

    /**
     * @param $listData
     * @param $listMemberData
     *
     * @return MailChimpListMember
     */
    protected function createListMember(array $listData, array $listMemberData): MailChimpListMember
    {
        $list = new MailChimpList($listData);
        $this->entityManager->persist($list);
        $this->entityManager->flush();

        $listMember = new MailChimpListMember($listMemberData);
        $listMember->setMailChimpList($list);

        $this->entityManager->persist($listMember);
        $this->entityManager->flush();

        return $listMember;
    }

    /**
     * @param $listData
     *
     * @return array
     */
    protected function createListMemberViaApi(array $listData): array
    {
        $this->post('/mailchimp/lists', $listData);
        $list = \json_decode($this->response->content(), true);

        $this->listId = $list['list_id'];
        $this->listMailChimpId = $list['mail_chimp_id'];

        return $list;
    }
}
