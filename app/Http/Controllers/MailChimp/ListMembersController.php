<?php
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpListMember;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mailchimp\Mailchimp;

class ListMembersController extends Controller
{
    /**
     * @var \Mailchimp\Mailchimp
     */
    private $mailChimp;

    /**
     * ListsController constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Mailchimp\Mailchimp $mailchimp
     */
    public function __construct(EntityManagerInterface $entityManager, Mailchimp $mailchimp)
    {
        parent::__construct($entityManager);

        $this->mailChimp = $mailchimp;
    }

    /**
     * Create MailChimp list member.
     *
     * @param string $listId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(string $listId, Request $request): JsonResponse
    {
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $list = $this->entityManager->getRepository(MailChimpList::class)->find($listId);

        if ($list === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }

        $listMember = new MailChimpListMember($request->all());
        $listMember->setMailChimpList($list);

        // Validate entity
        $validator = $this->getValidationFactory()->make($listMember->toMailChimpArray(), $listMember->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Save list member into db
            $this->saveEntity($listMember);
            // Save list member into MailChimp
            $response = $this->mailChimp->post("lists/{$list->getMailChimpId()}/members", $listMember->toMailChimpArray());
            // Set MailChimp id on the list and save list into db
            $this->saveEntity($listMember->setMailChimpId($response->get('id')));
        } catch (Exception $exception) {
            // Return error response if something goes wrong
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($listMember->toArray());
    }

    /**
     * Retrieve and return MailChimp list member.
     *
     * @param string $listId
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $listId, string $memberId): JsonResponse
    {
        /** @var MailChimpListMember $listMember */
        $listMember = $this->entityManager->getRepository(MailChimpListMember::class)
            ->findOneByListId($memberId, $listId);

        if ($listMember === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpListMember[%s] not found in MailChimpList[%s]', $memberId, $listId)],
                404
            );
        }

        return $this->successfulResponse($listMember->toArray());
    }

    /**
     * Update MailChimp list member.
     *
     * @param string $listId
     * @param string$memberId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(string $listId, string $memberId, Request $request): JsonResponse
    {
        /** @var MailChimpListMember $listMember */
        $listMember = $this->entityManager
            ->getRepository(MailChimpListMember::class)
            ->findOneByListId($memberId, $listId);

        if ($listMember === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpListMember[%s] not found in MailChimpList[%s]', $memberId, $listId)],
                404
            );
        }

        // Update list member properties
        $listMember->fill($request->all());

        // Validate entity
        $validator = $this->getValidationFactory()->make($listMember->toMailChimpArray(), $listMember->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Update list member into database
            $this->saveEntity($listMember);

            // Update list into MailChimp
            $requestUrl = \sprintf('lists/%s/members/%s', $listMember->getMailChimpList()->getMailChimpId(), $listMember->getSubscriberHash());
            $this->mailChimp->patch($requestUrl, $listMember->toMailChimpArray());
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($listMember->toArray());
    }

    /**
     * Remove MailChimp member from list.
     *
     * @param string $listId
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(string $listId, string $memberId): JsonResponse
    {
        /** @var MailChimpListMember $listMember */
        $listMember = $this->entityManager
            ->getRepository(MailChimpListMember::class)
            ->findOneByListId($memberId, $listId);

        if ($listMember === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpListMember[%s] not found in MailChimpList[%s]', $memberId, $listId)],
                404
            );
        }

        try {
            // Remove list from database
            $this->removeEntity($listMember);
            // Remove list from MailChimp
            $requestUrl = \sprintf('lists/%s/members/%s', $listMember->getMailChimpList()->getMailChimpId(), $listMember->getSubscriberHash());
            $this->mailChimp->delete($requestUrl);
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse([]);
    }
}
