<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\DataFixtures\SeasonFixtures;
use App\Entity\Season;
use App\Repository\SeasonRepository;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class SeasonTest extends ControllerBaseCase {
    // Change this to HTTP_OK when the site is public.
    private const ANON_RESPONSE_CODE = Response::HTTP_FOUND;

    private const TYPEAHEAD_QUERY = 'title';

    protected function fixtures() : array {
        return [
            SeasonFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * @group anon
     * @group index
     */
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/season/');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group user
     * @group index
     */
    public function testUserIndex() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/season/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group admin
     * @group index
     */
    public function testAdminIndex() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/season/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    /**
     * @group anon
     * @group show
     */
    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/season/1');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group user
     * @group show
     */
    public function testUserShow() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/season/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group admin
     * @group show
     */
    public function testAdminShow() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/season/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group anon
     * @group typeahead
     */
    public function testAnonTypeahead() : void {
        $this->client->request('GET', '/season/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        if (self::ANON_RESPONSE_CODE === Response::HTTP_FOUND) {
            // If authentication is required stop here.
            return;
        }
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(4, $json);
    }

    /**
     * @group user
     * @group typeahead
     */
    public function testUserTypeahead() : void {
        $this->login('user.user');
        $this->client->request('GET', '/season/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(4, $json);
    }

    /**
     * @group admin
     * @group typeahead
     */
    public function testAdminTypeahead() : void {
        $this->login('user.admin');
        $this->client->request('GET', '/season/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent());
        $this->assertCount(4, $json);
    }

    public function testAnonSearch() : void {
        $repo = $this->createMock(SeasonRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('season.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set(SeasonRepository::class, $repo);

        $crawler = $this->client->request('GET', '/season/search');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        if (self::ANON_RESPONSE_CODE === Response::HTTP_FOUND) {
            // If authentication is required stop here.
            return;
        }

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'season',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserSearch() : void {
        $repo = $this->createMock(SeasonRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('season.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set(SeasonRepository::class, $repo);

        $this->login('user.user');
        $crawler = $this->client->request('GET', '/season/search');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'season',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminSearch() : void {
        $repo = $this->createMock(SeasonRepository::class);
        $repo->method('searchQuery')->willReturn([$this->getReference('season.1')]);
        $this->client->disableReboot();
        $this->client->getContainer()->set(SeasonRepository::class, $repo);

        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/season/search');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'season',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group anon
     * @group edit
     */
    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/season/1/edit');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group user
     * @group edit
     */
    public function testUserEdit() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/season/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group edit
     */
    public function testAdminEdit() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/season/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'season[number]' => 12,
            'season[title]' => 'Updated Title',
            'season[alternativeTitle]' => 'Updated AlternativeTitle',
            'season[description]' => 'Updated Description',
            'season[podcast]' => 1,
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/season/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated Title")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated AlternativeTitle")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("Updated Description")')->count());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/season/new');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNewPopup() : void {
        $crawler = $this->client->request('GET', '/season/new_popup');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNew() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/season/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNewPopup() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/season/new_popup');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNew() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/season/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'season[number]' => '14',
            'season[title]' => 'New Title',
            'season[alternativeTitle]' => 'New AlternativeTitle',
            'season[description]' => 'New Description',
            'season[podcast]' => 1,
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Title")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New AlternativeTitle")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Description")')->count());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNewPopup() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/season/new_popup');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'season[number]' => '15',
            'season[title]' => 'New Title',
            'season[alternativeTitle]' => 'New AlternativeTitle',
            'season[description]' => 'New Description',
            'season[podcast]' => 1,
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Title")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New AlternativeTitle")')->count());
        $this->assertSame(1, $responseCrawler->filter('td:contains("New Description")')->count());
    }

    /**
     * @group admin
     * @group delete
     */
    public function testAdminDelete() : void {
        $repo = self::$container->get(SeasonRepository::class);
        $preCount = count($repo->findAll());

        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/season/1');
        $form = $crawler->selectButton('Delete')->form();
        $this->client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($repo->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }

    public function testAnonNewImage() : void {
        $formCrawler = $this->client->request('GET', '/season/1/new_image');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserNewImage() : void {
        $this->login('user.user');
        $formCrawler = $this->client->request('GET', '/season/1/new_image');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNewImage() : void {
        $this->login('user.admin');
        $upload = new UploadedFile(__DIR__ . '/../data/35597651312_a188de382c_c.jpg', 'chicken.jpg');

        $formCrawler = $this->client->request('GET', '/season/1/new_image');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $form = $formCrawler->selectButton('Create')->form([
            'image[imageFile]' => $upload,
            'image[public]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/season/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(2, $responseCrawler->filter('div:contains("The new image has been saved")')->count());

        $this->entityManager->clear();
        $season = $this->entityManager->find(Season::class, 1);

        foreach ($season->getImages() as $image) {
            $this->cleanUp($image->getImageFile());
            $this->cleanUp($image->getThumbFile());
        }
    }

    public function testAnonEditImage() : void {
        $formCrawler = $this->client->request('GET', '/season/1/edit_image/1');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserEditImage() : void {
        $this->login('user.admin');
        $upload = new UploadedFile(__DIR__ . '/../data/35597651312_a188de382c_c.jpg', 'chicken.jpg');

        $formCrawler = $this->client->request('GET', '/season/1/new_image');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $form = $formCrawler->selectButton('Create')->form([
            'image[imageFile]' => $upload,
            'image[public]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/season/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(2, $responseCrawler->filter('div:contains("The new image has been saved")')->count());

        $this->entityManager->clear();
        $season = $this->entityManager->find(Season::class, 1);

        foreach ($season->getImages() as $image) {
            $this->cleanUp($image->getImageFile());
            $this->cleanUp($image->getThumbFile());
        }

        $this->login('user.user');
        $formCrawler = $this->client->request('GET', '/season/1/edit_image/1');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEditImage() : void {
        $this->login('user.admin');
        $upload = new UploadedFile(__DIR__ . '/../data/24708385605_c5387e7743_c.jpg', 'cat.jpg');

        $formCrawler = $this->client->request('GET', '/season/1/new_image');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $form = $formCrawler->selectButton('Create')->form([
            'image[imageFile]' => $upload,
            'image[public]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/season/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(2, $responseCrawler->filter('div:contains("The new image has been saved")')->count());

        $this->entityManager->clear();
        $season = $this->entityManager->find(Season::class, 1);

        foreach ($season->getImages() as $image) {
            $this->cleanUp($image->getImageFile());
            $this->cleanUp($image->getThumbFile());
        }

        $upload = new UploadedFile(__DIR__ . '/../data/32024919067_c2c18aa1c5_c.jpg', 'dog.jpg');
        $formCrawler = $this->client->request('GET', '/season/1/edit_image/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $form = $formCrawler->selectButton('Update')->form([
            'image[newImageFile]' => $upload,
            'image[public]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/season/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(2, $responseCrawler->filter('div:contains("The image has been updated")')->count());

        $this->entityManager->clear();
        $season = $this->entityManager->find(Season::class, 1);

        foreach ($season->getImages() as $image) {
            $this->cleanUp($image->getImageFile());
            $this->cleanUp($image->getThumbFile());
        }
    }
}
