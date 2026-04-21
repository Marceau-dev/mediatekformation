<?php

namespace App\Tests\Validations;

use App\Entity\Formation;
use App\Form\FormationType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class FormationTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);
    }

    public function testPublishedAtCannotBeGreaterThanToday(): void
    {
        $formation = new Formation();

        $form = $this->formFactory->create(FormationType::class, $formation);

        $form->submit([
            'title' => 'Formation test',
            'description' => 'Description test',
            'videoId' => 'abc123',
            'publishedAt' => (new \DateTime('+1 day'))->format('Y-m-d'),
            'playlist' => '',
            'categories' => [],
        ]);

        $this->assertFalse($form->isValid());
        $this->assertGreaterThan(0, count($form->get('publishedAt')->getErrors(true)));
    }

    public function testPublishedAtCanBeToday(): void
    {
        $formation = new Formation();

        $form = $this->formFactory->create(FormationType::class, $formation);

        $form->submit([
            'title' => 'Formation test',
            'description' => 'Description test',
            'videoId' => 'abc123',
            'publishedAt' => (new \DateTime('today'))->format('Y-m-d'),
            'playlist' => '',
            'categories' => [],
        ]);

        $this->assertCount(0, $form->get('publishedAt')->getErrors(true));
    }
}
