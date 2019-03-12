<?php
declare(strict_types=1);

namespace Tests\App\TestCases\MailChimp;

/**
 * Class MailChimpData
 * @package Tests\App\TestCases\MailChimp
 */
class MailChimpData
{
    /**
     * @var array
     */
    public static $listData = [
        'name' => 'New list',
        'permission_reminder' => 'You signed up for updates on Greeks economy.',
        'email_type_option' => false,
        'contact' => [
            'company' => 'Doe Ltd.',
            'address1' => 'DoeStreet 1',
            'address2' => '',
            'city' => 'Doesy',
            'state' => 'Doedoe',
            'zip' => '1672-12',
            'country' => 'US',
            'phone' => '55533344412'
        ],
        'campaign_defaults' => [
            'from_name' => 'John Doe',
            'from_email' => 'john@doe.com',
            'subject' => 'My new campaign!',
            'language' => 'US'
        ],
        'visibility' => 'prv',
        'use_archive_bar' => false,
        'notify_on_subscribe' => 'notify@loyaltycorp.com.au',
        'notify_on_unsubscribe' => 'notify@loyaltycorp.com.au'
    ];

    public static $listMemberData = [
        'email_address' => 'koibpas3@gmail.com',
        'email_type' => 'html',
        'status' => 'subscribed',
        'location' => [
            'latitude' => 53.741158,
            'longitude' => 91.413709
        ],
        "tags" => [
            'a tag',
            'another tag'
        ],
    ];
}
