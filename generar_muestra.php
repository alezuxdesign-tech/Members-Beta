<?php
// Script de Muestra de PHP GD Avanzado
$width = 600;
$height = 900;
$image = imagecreatetruecolor($width, $height);

// Activar Alpha (Transparencias)
imagealphablending($image, true);
imagesavealpha($image, true);

// Paletas premium aleatorias para romper la monotonía
$palettes = [
    ['bg1' => [15, 23, 42], 'bg2' => [88, 28, 135], 'accent' => [236, 72, 153]],  // Indigo/Pink
    ['bg1' => [15, 23, 42], 'bg2' => [3, 105, 161], 'accent' => [56, 189, 248]],   // Ocean/Blue
    ['bg1' => [24, 24, 27], 'bg2' => [180, 83, 9], 'accent' => [251, 191, 36]],    // Dark Amber
    ['bg1' => [17, 24, 39], 'bg2' => [4, 120, 87], 'accent' => [52, 211, 153]],    // Emerald
    ['bg1' => [23, 23, 23], 'bg2' => [153, 27, 27], 'accent' => [248, 113, 113]],  // Ruby
];
$p = $palettes[array_rand($palettes)];

// 1. Degradado Diagonal Suave
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $factor = ($x + $y) / ($width + $height);
        $r = (int)($p['bg1'][0] + ($p['bg2'][0] - $p['bg1'][0]) * $factor);
        $g = (int)($p['bg1'][1] + ($p['bg2'][1] - $p['bg1'][1]) * $factor);
        $b = (int)($p['bg1'][2] + ($p['bg2'][2] - $p['bg1'][2]) * $factor);
        imagesetpixel($image, $x, $y, imagecolorallocate($image, $r, $g, $b));
    }
}

// 2. Luz de Acento (Glow) en una esquina
$glow_x = $width - 100;
$glow_y = 200;
$max_radius = 400;
for ($r = $max_radius; $r > 0; $r -= 2) {
    // Calculamos el alpha de 127 (transparente) a 0 (opaco). Hacemos un glow muy suave.
    $alpha = (int)(127 - (127 * ($r / $max_radius) * 0.8)); 
    if ($alpha > 127) $alpha = 127;
    $glow_color = imagecolorallocatealpha($image, $p['accent'][0], $p['accent'][1], $p['accent'][2], $alpha);
    imagefilledellipse($image, $glow_x, $glow_y, $r, $r, $glow_color);
}

// 3. Patrón de puntos sutil (Textura premium)
$dot_color = imagecolorallocatealpha($image, 255, 255, 255, 120); // 120/127 = muy transparente
for ($x = 0; $x < $width; $x += 20) {
    for ($y = 0; $y < $height; $y += 20) {
        imagesetpixel($image, $x, $y, $dot_color);
    }
}

// Helper: Rectángulo con bordes redondeados
function drawRoundRect($img, $x1, $y1, $w, $h, $radius, $color) {
    $x2 = $x1 + $w; $y2 = $y1 + $h;
    imagefilledrectangle($img, $x1+$radius, $y1, $x2-$radius, $y2, $color);
    imagefilledrectangle($img, $x1, $y1+$radius, $x2, $y2-$radius, $color);
    imagefilledarc($img, $x1+$radius, $y1+$radius, $radius*2, $radius*2, 180, 270, $color, IMG_ARC_PIE);
    imagefilledarc($img, $x2-$radius, $y1+$radius, $radius*2, $radius*2, 270, 360, $color, IMG_ARC_PIE);
    imagefilledarc($img, $x1+$radius, $y2-$radius, $radius*2, $radius*2, 90, 180, $color, IMG_ARC_PIE);
    imagefilledarc($img, $x2-$radius, $y2-$radius, $radius*2, $radius*2, 0, 90, $color, IMG_ARC_PIE);
}

// 4. Glassmorphism Card (Tarjeta translúcida)
$card_bg = imagecolorallocatealpha($image, 255, 255, 255, 100); // Fondo semi-blanco
drawRoundRect($image, 40, $height - 350, $width - 80, 300, 30, $card_bg);

// 5. Botón de "Play" flotante
$play_bg = imagecolorallocate($image, $p['accent'][0], $p['accent'][1], $p['accent'][2]);
imagefilledellipse($image, 100, $height - 350, 80, 80, $play_bg);
$white = imagecolorallocate($image, 255, 255, 255);
// Triangulo play
$points = [ 90, $height - 365, 90, $height - 335, 115, $height - 350 ];
imagefilledpolygon($image, $points, 3, $white);

// Fuentes
$font_bold = __DIR__ . '/assets/fonts/Roboto-Bold.ttf';
$font_regular = __DIR__ . '/assets/fonts/Roboto-Regular.ttf';

if (file_exists($font_bold)) {
    // 6. Badge de Módulo
    $badge_color = imagecolorallocatealpha($image, 0, 0, 0, 70);
    drawRoundRect($image, 70, $height - 310, 140, 40, 20, $badge_color);
    imagettftext($image, 14, 0, 85, $height - 285, $white, $font_bold, "MÓDULO 1");

    // 7. Título principal
    $title = "ATAS Indicadores y herramientas de análisis técnico";
    $words = explode(' ', $title);
    $lines = []; $current_line = '';
    foreach ($words as $word) {
        $bbox = imagettfbbox(24, 0, $font_bold, $current_line . $word . ' ');
        if ($bbox[2] > ($width - 140)) {
            $lines[] = trim($current_line);
            $current_line = $word . ' ';
        } else {
            $current_line .= $word . ' ';
        }
    }
    $lines[] = trim($current_line);
    
    $text_y = $height - 220;
    foreach ($lines as $line) {
        imagettftext($image, 24, 0, 70, $text_y, $white, $font_bold, $line);
        $text_y += 35;
    }

    // 8. Meta text
    $light_gray = imagecolorallocatealpha($image, 255, 255, 255, 30);
    imagettftext($image, 12, 0, 70, $height - 80, $light_gray, $font_regular, "Lección en video • Alta Calidad");
}

// Logo arriba
imagettftext($image, 20, 0, 40, 60, $white, $font_bold, "ALEZUX");

// Guardar
imagejpeg($image, __DIR__ . '/muestra_premium.jpg', 100);
imagedestroy($image);

echo "Imagen generada en: " . __DIR__ . '/muestra_premium.jpg';
