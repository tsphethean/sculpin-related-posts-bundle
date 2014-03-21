<?php

namespace Tsphethean\Sculpin\Bundle\RelatedPostsBundle;

use Sculpin\Core\Sculpin;
use Sculpin\Core\Event\SourceSetEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Sculpin\Core\Permalink\SourcePermalinkFactory;

/**
 * Related Posts Generator.
 *
 * @author Tom Phethean <www.tsphethean.co.uk>
 */
class RelatedPostsGenerator implements EventSubscriberInterface {

  /**
   * Permalink factory
   *
   * @var SourcePermalinkFactory
   */
  protected $permalinkFactory;

  public function __construct(SourcePermalinkFactory $permalinkFactory) {
    $this->permalinkFactory = $permalinkFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return array(
      Sculpin::EVENT_BEFORE_RUN => array('beforeRun', 100),
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

        // remove self from list of related sources
        unset($tagMatchCount[$source->sourceid()]);
        asort($tagMatchCount);

        // Get information about the matching tags
        $relatedSources = array();
        foreach ($tagMatchCount as $match => $count) {
          // @TODO - make limit configurable
          if (count($relatedSources) == 5) {
            break;
          }

          if (!$relatedSource->data()->get('draft')) {
            $relatedSources[] = array(
              // @TODO - figure out why the title won't come through in the source.
              'title' => $relatedSource->data()->get('title'),
              'source' => $relatedSource,
            );
          }
        }

        $source->data()->set('related', $relatedSources);
      }
    }
  }
}
