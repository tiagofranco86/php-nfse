<?php

namespace NFePHP\NFSe\Models\Smartpd\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Smartpd\Factories\Factory;

class ConsultarUrlNota extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteCNPJCPF
     * @param $im
     * @param $protocolo
     * @return mixed
     */
    public function render(
        $im,
        $codigoMunicipio,
        $numeroNfse,
        $serieNfse
    ) {
        $dom = new Dom('1.0', 'utf-8');
               
        // //Adiciona a Inscrição Municipal na tag Prestador
        $dom->addChild(
            $dom,
            'codigoMunicipio',
            $codigoMunicipio,
            true,
            "codigoMunicipio",
            true
        );

        //Adiciona o Cnpj na tag Prestador
        $dom->addChild(
            $dom,
            'numeroNfd',
            $numeroNfse,
            true,
            "numeroNfd",
            true
        );
        // //Adiciona a Inscrição Municipal na tag Prestador
        $dom->addChild(
            $dom,
            'serieNfd',
            $serieNfse,
            true,
            "serieNfd",
            true
        );

        //Adiciona o Cnpj na tag Prestador
        $dom->addChild(
            $dom,
            'inscricaoMunicipal',
            $im,
            true,
            "Inscricao Municipal",
            true
        );

        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);
        
        return $body;
    }
}
