<?php

namespace Drupal\website_zip_formatter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\file\Entity\FileInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\Mime\MimeTypes;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller to extract files from ZIP and return them for display/download.
 */
class PrintZipController extends ControllerBase {
  protected FileSystemInterface $fileSystem;

  public function __construct(FileSystemInterface $file_system, LoggerInterface $logger) {
    $this->fileSystem = $file_system;
    $this->logger = $logger;
  }

  public static function create (ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('logger.channel.default'),
    );
  }

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
    $uri = $file->getFileUri();
    $realpath = $this->fileSystem->realpath($uri);
    if (!$realpath || !file_exists($realpath)) {
      throw new NotFoundHttpException();
    }

    if (strtolower(pathinfo($realpath, PATHINFO_EXTENSION)) !== 'zip') {
      throw new NotFoundHttpException();
    }

    $zip = new \ZipArchive();
    if ($zip->open($realpath) !== TRUE) {
      $this->logger->error('Failed to open ZIP file @fid', ['@fid' => $file->id()]);
      throw new NotFoundHttpException();
    }

    $info = $zip->statName($path);
    if ($info === false) {
      $this->logger->error('Could not find path @path in ZIP file @fid', ['@fid' => $file->id(), '@path' => $path]);
      throw new NotFoundHttpException();
    }

    $content_type = \Drupal::service('file.mime_type.guesser.extension')->guessMimeType($path);

    $contents = $zip->getFromName($path);
    $zip->close();

    return new Response($contents, 200, [
      'Content-Length' => $info['size'],
      'Content-Type' => "{$content_type};charset=UTF-8",
      'Last-Modified' => gmdate("D, d M Y H:i:s T", $info['mtime'])
    ]);
  }
}
