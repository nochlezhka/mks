<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of ImageStringToFileTransformer
 *
 * @author Juanjo García
 */
class ImageStringToFileTransformer implements DataTransformerInterface
{
    /**
     * Transforms a string (base64) to an object (File).
     *
     * @param  string $imageString
     * @return File|null
     * @throws TransformationFailedException if no object (File)
     */
    public function reverseTransform($imageString)
    {
        // no base64? It's optional, so that's ok
        if (!$imageString) {
            return;
        }

        preg_match('/data:([^;]*);base64,(.*)/', $imageString, $matches);

        if (empty($matches[1])) {
            return $imageString;
        }

        $mimeType = $matches[1];
        $imagenDecodificada = base64_decode($matches[2]);
        $filePath = sys_get_temp_dir() . "/" . uniqid() . '.png';
        file_put_contents($filePath, $imagenDecodificada);

        $file = new UploadedFile($filePath, "client.png", $mimeType, null, null, true);

        if (null === $file) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!', $imageString
            ));
        }

        return $file;
    }

    /**
     * Transforms an object (File) to a string (base64).
     *
     * @param  File|null $file
     * @return string
     */
    public function transform($file)
    {
        return '';
    }
}
