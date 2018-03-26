<?php
/**
 * @file
 */

namespace Phloem\Core\Event;

use PHPUnit\Framework\TestCase;

class EventManagerTest extends TestCase
{

    /**
     *
     */
    public function testEvents()
    {
        $manager = new EventManager();

        $triggered = [];

        $manager->attach('test', function($event) use (&$triggered) {
            $triggered[] = 'one';
        });

        $manager->attach('test', function ($event) use (&$triggered) {
            $triggered[] = 'two';
        });

        $self = $this;
        $manager->attach('test', function (Event $event) use (&$triggered, $self) {
            $self->assertEquals('test', $event->getName());
            $self->assertEquals(['test'], $event->getParams());

            $triggered[] = 'three';
        }, 1);

        // Triggers the events.
        $manager->trigger('test', ['test']);

        // Check that prioritized events are ordered correctly.
        $this->assertEquals(['three', 'one', 'two'], $triggered);

        // Triggers a non-existent event.
        $manager->trigger('non-existent');
    }

}
