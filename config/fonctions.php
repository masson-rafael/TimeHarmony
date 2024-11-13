<?php

/**
 * Redimensionne une image
 *
 * @param string|null $img_src Chemin de l'image source
 * @param string|null $img_dest Chemin de l'image de destination
 * @param string|null $dst_w Largeur de l'image de destination
 * @param string|null $dst_h Hauteur de l'image de destination
 * @return string|null Chemin de l'image recadree
 */
function redimage(?string $img_src, ?string $img_dest, ?string $dst_w, ?string $dst_h): ?string {
    // Lit les dimensions de l'image
    $size = GetImageSize("$img_src");
    $src_w = $size[0]; $src_h = $size[1];

    // Teste les dimensions tenant dans la zone
    $test_h = round(($dst_w / $src_w) * $src_h);
    $test_w = round(($dst_h / $src_h) * $src_w);

    // Crée une image vierge aux bonnes dimensions
    $dst_im = ImageCreateTrueColor($dst_w, $dst_h);

    // Copie l'image initiale redimensionnée
    $src_im = ImageCreateFromJpeg("$img_src");

    ImageCopyResampled($dst_im, $src_im, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

    // Sauvegarde la nouvelle image
    ImageJpeg($dst_im, "$img_dest");

    // Détruis les tampons
    ImageDestroy($dst_im);
    ImageDestroy($src_im);

    // Return le chemin de l'image recadree
    return $img_dest;
}