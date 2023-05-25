<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService
{
    private $params;

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        // On donne un nouveau nom à l'image
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        // On récupère les infos de l'image
        $picture_infos = getImageSize($picture);

        if($picture_infos === false){
            throw new Exception(' Format d\'image incorrect');
        }

        // On vérifie le format de l'image
        switch($picture_infos['mime'])
        {
            case 'image/png':
                $picture_source = imageCreateFromPng($picture);
                break;
            case 'image/jpeg':
                $picture_source = imageCreateFromJpeg($picture);
                break;
            case 'image/webp':
                $picture_source = imageCreateFromWebp($picture);
                break;
            default:
                throw new Exception ('Format d\'image incorrect');
        } //erreur imcomprie ici

        // On recadre l'image
        // On récupère les dimensions
        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

        // On vérifie l'orientation de l'image
        // <=> =triple condition (-1 si inférieur, 0 si égal, +1 si supérieur)
        switch($imageWidth <=> $imageHeight){
            case -1: //portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
        
            case 0: //carré
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
        
            case +1: //paysage
                $squareSize = $imageWidth;
                $src_x = ($imageHeight - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        // On crée une nouvelle image 'vierge'
        $resized_picture = imageCreateTrueColor($width, $height);

        imageCopyResampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') . $folder;

        //On crée le dossier de destination s'il n'existe pas
        if(!file_exists($path . '/mini/'))
        {
            mkdir($path . '/mini/', 0755, true);
        }

        // On stocke l'image recadrée
        imagewebp($resized_picture, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }
    public function delete(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250)
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

