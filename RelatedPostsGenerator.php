<?php

namespace Tsphethean\Sculpin\Bundle\RelatedPostsBundle;

use Sculpin\Core\Sculpin;
use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Source\SourceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Related Posts Generator.
 *
 * @author Tom Phethean <www.tsphethean.co.uk>
 */
class RelatedPostsGenerator implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return array(
      Sculpin::EVENT_BEFORE_RUN => array('beforeRun', 1000),
    );
  }

  public function beforeRun(SourceSetEvent $sourceSetEvent) {

    $sourceSet = $sourceSetEvent->sourceSet();
    $updatedSources = $sourceSet->updatedSources();
    $allSources = $sourceSet->allSources();

    $tagsMap = array();

    // Build a map of all the tags on each source
    foreach ($updatedSources as $source) {
      // Get the tags of this source.
      if ($sourceTags = $source->data()->get('tags')) {
        foreach ($sourceTags as $tag) {
          $tagsMap[$tag][] = $source->sourceId();
        }
      }

      if ($source->isGenerated()) {
        // Skip generated sources.
        continue;
      }
    }

    // Re-run through each source, identifying sources with matching tags.
    foreach ($updatedSources as $source) {
      $tagMatch = array();

      if ($sourceTags = $source->data()->get('tags')) {
        // for each tag that this post has...
        foreach ($sourceTags as $tag) {
          // get the mapped sources for this tag
          $tagMatch = array_merge($tagMatch, $tagsMap[$tag]);
        }
        $tagMatchCount = array_count_values($tagMatch);
        asort($tagMatchCount);
        // need to remove self...

        // Get information about the matching tags
        $relatedSources = array();
        foreach ($tagMatchCount as $match => $count) {
          $relatedSource = $allSources[$match];

          $relatedSources[] = array(
            'title' => $relatedSource->data()->get('title'),
            'url' => $relatedSource->relativePathname(),
          );
        }

        $source->data()->set('related', $relatedSources);
      }
    }
  }

}