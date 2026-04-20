<?php

namespace Drupal\website_zip_formatter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\file\Entity\FileInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller to extract files from ZIP and return them for display/download.
 */
class PrintZipController extends ControllerBase {

  /**
   * Serve a file from inside a ZIP.
   *
   * If $path is empty, serves index.html (or index.htm).
   *
   * @param \Drupal\file\Entity\File $fid
   *   File entity ID.
   * @param string $path
   *   (Optional) URL-encoded part of the path inside the ZIP.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function serve(File $file, $path) {
    return new Response("({$file->id()}={$path})", 200);
  }
}
