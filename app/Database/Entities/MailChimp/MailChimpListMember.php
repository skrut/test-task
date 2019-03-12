<?php
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;

/**
 * @ORM\Entity(repositoryClass="App\Database\Repositories\MailChimpListMemberRepository")
 */
class MailChimpListMember extends MailChimpEntity
{
    /**
     * @ORM\Column(name="email_address", type="string")
     *
     * @var string
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="email_type", type="string", nullable=true)
     *
     * @var string
     */
    private $emailType;

    /**
     * @ORM\Column(name="interests", type="array", nullable=true)
     *
     * @var array
     */
    private $interests;

    /**
     * @ORM\Column(name="language", type="string", nullable=true)
     *
     * @var string
     */
    private $language;

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $listMemberId;

    /**
     * @ORM\Column(name="location", type="array", nullable=true)
     *
     * @var array
     */
    private $location;

    /**
     * @ORM\Column(name="mail_chimp_id", type="string", nullable=true)
     *
     * @var string
     */
    private $mailChimpId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Database\Entities\MailChimp\MailChimpList", inversedBy="members")
     * @ORM\JoinColumn(name="mail_chimp_list_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $mailChimpList;

    /**
     * @ORM\Column(name="marketing_permissions", type="array", nullable=true)
     *
     * @var array
     */
    private $marketingPermissions;

    /**
     * @ORM\Column(name="merge_fields", type="array", nullable=true)
     *
     * @var array
     */
    private $mergeFields;

    /**
     * @ORM\Column(name="status", type="string")
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     *
     * @var array
     */
    private $tags;

    /**
     * @ORM\Column(name="vip", type="boolean", nullable=true)
     *
     * @var boolean
     */
    private $vip;

    /**
     * @return string
     */
    public function getMailChimpId(): ?string
    {
        return $this->mailChimpId;
    }

    /**
     * @param string $mailChimpId
     * @return $this
     */
    public function setMailChimpId(string $mailChimpId): MailChimpListMember
    {
        $this->mailChimpId = $mailChimpId;

        return $this;
    }

    /**
     * Get id.
     *
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->listMemberId;
    }

    /**
     * @param string $emailAddress
     * @return $this
     */
    public function setEmailAddress(string $emailAddress): MailChimpListMember
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @param string $emailType
     * @return $this
     */
    public function setEmailType(string $emailType): MailChimpListMember
    {
        $this->emailType = $emailType;

        return $this;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): MailChimpListMember
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array $mergeFields
     * @return $this
     */
    public function setMergeFields(array $mergeFields): MailChimpListMember
    {
        $this->mergeFields = $mergeFields;

        return $this;
    }

    /**
     * @param array $interests
     * @return $this
     */
    public function setInterests(array $interests): MailChimpListMember
    {
        $this->interests = $interests;

        return $this;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language): MailChimpListMember
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param bool $vip
     * @return $this
     */
    public function setVip(bool $vip): MailChimpListMember
    {
        $this->vip = $vip;

        return $this;
    }

    /**
     * @param array $location
     * @return $this
     */
    public function setLocation(array $location): MailChimpListMember
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @param array $marketingPermissions
     * @return $this
     */
    public function setMarketingPermissions(array $marketingPermissions): MailChimpListMember
    {
        $this->marketingPermissions = $marketingPermissions;

        return $this;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags): MailChimpListMember
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return MailChimpList
     */
    public function getMailChimpList(): MailChimpList
    {
        return $this->mailChimpList;
    }

    /**
     * @param mixed $mailChimpList
     * @return $this
     */
    public function setMailChimpList($mailChimpList): MailChimpListMember
    {
        $this->mailChimpList = $mailChimpList;

        return $this;
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'email_address' => 'required|email',
            'email_type' => 'nullable|in:html,text',
            'status' => 'required|string|in:subscribed,unsubscribed,cleaned,pending',
            'merge_fields' => 'nullable|array',
            'interests' => 'nullable|array',
            'language' => 'nullable|string|max:5',
            'vip' => 'nullable|boolean',
            'location' => 'nullable|array',
            'location.latitude' => 'required_with:location|numeric',
            'location.longitude' => 'required_with:location|numeric',
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $str = new Str();

        $excludedFields = ['mailChimpList'];
        foreach (\get_object_vars($this) as $property => $value) {
            if (in_array($property, $excludedFields)) {
                continue;
            }

            $array[$str->snake($property)] = $value;
        }

        return $array;
    }

    /**
     * @return string
     */
    public function getSubscriberHash(): string
    {
        return md5(mb_strtolower($this->emailAddress, 'utf-8'));
    }
}
