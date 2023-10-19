<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService
{
    private $params;

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 800, ?int $height = 400)
    {
        // On donne un nouveau nom à l'image
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        // On récupère les infos de l'image
        $picture_infos = getImageSize($picture);

        // Limitation du type de fichier
        if($picture_infos === false){
            throw new Exception(' Format d\'image incorrect');
        }

        // Limitation de la taille maximale
        $MAX_SIZE = 5 * 1024 * 1024;
        if ($picture->getSize() > $MAX_SIZE) {
            throw new Exception('Fichier trop volumineux');
        }
        // Ajout de la vérification du type MIME
        $mime_type = $picture->getMimeType();
        if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/webp'])) {
            throw new Exception('Type de fichier non supporté');
        }

        // On vérifie le format de l'image
        switch($picture_infos['mime'])
        {
            case 'image/png':
                $picture_source = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $picture_source = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $picture_source = imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception ('Format d\'image incorrect');
        } //erreur imcomprie ici

        // On recadre l'image
        // On récupère les dimensions
        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];


        // On vérifie l'orientation de l'image
        switch ($imageWidth <=> $imageHeight){
            case -1: // portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            case 0: // carré
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1: // paysage
                $squareSize = $imageHeight;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        // On crée une nouvelle image "vierge"
        $resized_picture = imagecreatetruecolor($width, $height);

        imagecopyresampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') . $folder;

        // On crée le dossier de destination s'il n'existe pas
        if(!file_exists($path . '/mini/')){
            mkdir($path . '/mini/', 0755, true);
        }

        // On stocke l'image recadrée
        imagewebp($resized_picture, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }   
    public function delete(UploadedFile $picture, ?string $folder = '', ?int $width = 800, ?int $height = 400)
    {
        if($fichier !== 'default.webp')
        {
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $fichier;

            if(file_exists($mini)){
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;

            if(file_exists($original)){
                unlink($mini);
                $success = true;
            }
            return $success;
        }
        return false;
    }
    public function __construct(ParameterBagInterface $params){
        $this->params = $params;
    }
}   