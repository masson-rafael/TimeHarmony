<?php

function redimage($img_src, $img_dest, $dst_w, $dst_h) {
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