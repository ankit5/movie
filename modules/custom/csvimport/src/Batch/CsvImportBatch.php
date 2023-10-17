<?php

namespace Drupal\csvimport\Batch;

// @codingStandardsIgnoreStart
// Node can be used later to actually create nodes. See commented code block
// in csvimportImportLine() below. Since it's unused right now, we hide it from
// coding standards linting.
use Drupal\Core\File\FileSystemInterface;
use Drupal\node\Entity\Node;

// @codingStandardsIgnoreEnd

/**
 * Methods for running the CSV import in a batch.
 *
 * @package Drupal\csvimport
 */
class CsvImportBatch {

  /**
   * Handle batch completion.
   *
   *   Creates a new CSV file containing all failed rows if any.
   */
  public static function csvimportImportFinished($success, $results, $operations) {

    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One post processed.', '@count posts processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()->addMessage($message);
  }
  

  /**
   * Remember the uploaded CSV filename.
   *
   * @TODO Is there a better way to pass a value from inception of the batch to
   * the finished function?
   */
  public static function csvimportRememberFilename($filename, &$context) {

    $context['results']['uploaded_filename'] = $filename;
  }

  /**
   * Process a single line.
   */
  public static function csvimportImportLine($line, &$context) {

    $context['results']['rows_imported']++;
    $line = array_map('base64_decode', $line);
   
    // Simply show the import row count.
    $context['message'] = t('Importing row !c', ['!c' => $context['results']['rows_imported']]);

    // Alternatively, our example CSV happens to have the title in the
    // third column, so we can uncomment this line to display "Importing
    // Blahblah" as each row is parsed.
    //
    // You can comment out the line above if you uncomment this one.
    $context['message'] = t('Importing %title', ['%title' => $line[2]]);

    // In order to slow importing and debug better, we can uncomment
    // this line to make each import slightly slower.
    // @codingStandardsIgnoreStart
    //usleep(2500);

    // @codingStandardsIgnoreEnd
    // Convert the line of the CSV file into a new node.
   
    if ($context['results']['rows_imported'] > 1) { // Skip header line.
      print_r($line);
      exit;
      
     /* @var \Drupal\node\NodeInterface $node */
     $node = Node::create([
       'type'  => 'article',
       'title' => $line[2],
       'body'  => $line[0],
     ]);
     $node->save();
    }
    

    // If the first two columns in the row are "ROW", "FAILS" then we
    // will add that row to the CSV we'll return to the importing person
    // after the import completes.
    if ($line[1] == 'ROW' && $line[2] == 'FAILS') {
      $context['results']['failed_rows'][] = $line;
    }
  }

}
