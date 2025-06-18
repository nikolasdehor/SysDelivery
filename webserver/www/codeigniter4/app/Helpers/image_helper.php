<?php

/**
 * Helper para tratamento de imagens
 * Centraliza a lógica de verificação se uma imagem é externa ou local
 */

if (!function_exists('getImageSrc')) {
    /**
     * Retorna a URL correta para exibir uma imagem
     * Verifica se é um link externo ou arquivo local
     * 
     * @param string $imageLink O link da imagem (pode ser URL externa ou caminho local)
     * @return string URL completa para exibir a imagem
     */
    function getImageSrc($imageLink)
    {
        if (empty($imageLink)) {
            return base_url('assets/images/no-image.svg');
        }

        // Verificar se é um link externo
        $isExternalLink = str_starts_with($imageLink, 'http://') ||
                         str_starts_with($imageLink, 'https://');

        return $isExternalLink ? $imageLink : base_url('assets/' . $imageLink);
    }
}

if (!function_exists('isExternalImage')) {
    /**
     * Verifica se uma imagem é um link externo
     * 
     * @param string $imageLink O link da imagem
     * @return bool True se for link externo, false se for arquivo local
     */
    function isExternalImage($imageLink)
    {
        return str_starts_with($imageLink, 'http://') || 
               str_starts_with($imageLink, 'https://');
    }
}

if (!function_exists('getImageTag')) {
    /**
     * Gera uma tag img completa com fallback para imagem não encontrada
     * 
     * @param string $imageLink O link da imagem
     * @param string $alt Texto alternativo
     * @param string $class Classes CSS
     * @param string $style Estilos CSS inline
     * @param array $attributes Atributos adicionais
     * @return string Tag img HTML completa
     */
    function getImageTag($imageLink, $alt = '', $class = '', $style = '', $attributes = [])
    {
        $src = getImageSrc($imageLink);
        $fallbackSrc = base_url('assets/images/no-image.svg');

        $attrs = [];
        $attrs[] = 'src="' . esc($src) . '"';
        $attrs[] = 'alt="' . esc($alt) . '"';

        if (!empty($class)) {
            $attrs[] = 'class="' . esc($class) . '"';
        }

        if (!empty($style)) {
            $attrs[] = 'style="' . esc($style) . '"';
        }

        $attrs[] = 'onerror="this.src=\'' . $fallbackSrc . '\'; this.alt=\'Imagem não encontrada\';"';

        // Adicionar atributos extras
        foreach ($attributes as $key => $value) {
            $attrs[] = esc($key) . '="' . esc($value) . '"';
        }

        return '<img ' . implode(' ', $attrs) . '>';
    }
}
