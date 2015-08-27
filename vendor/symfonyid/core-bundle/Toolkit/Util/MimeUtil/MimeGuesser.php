<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\Util\MimeUtil;

final class MimeGuesser
{
    private $mimeMapping = array(
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'video/mp4'  => 'mp4',
        'application/ogg'  => 'ogv',
    );

    /**
     * @param $mime
     * @return string
     */
    public function getExtension($mime)
    {
        if (in_array($mime, $this->mimeMapping)) {
            return $this->mimeMapping($mime);
        }
    }

    /**
     * @param $filePath
     * @return array
     */
    public function getMimeType($filePath)
    {
        $mime = finfo_buffer(finfo_open(), $filePath, FILEINFO_MIME_TYPE);

        return $this->normalizeMime($mime);
    }

    /**
     * @param $mime
     * @return array
     */
    public function normalizeMime($mime)
    {
        $exploded = explode('/', $mime);

        return array(
            'type' => $exploded[0],
            'encode' => $exploded[1],
            'mime' => $mime,
        );
    }
}