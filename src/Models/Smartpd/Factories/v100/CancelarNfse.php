<?php

namespace NFePHP\NFSe\Models\Smartpd\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Smartpd\Factories\Factory;

class CancelarNfse extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $datacancelamento
     * @param $motivocancelamento
     * @param $inscricaoMunicipal
     * @param $nfseNumero
     * @return string
     */
    public function render(
        $versao,
        $datacancelamento,
        $motivocancelamento,
        $inscricaoMunicipal,
        $nfseNumero
    ) {        
        $xsd = "cancelanfd";


        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('CancelarNfseEnvio');
        //$root->setAttribute('xmlns', $this->xmlns);

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $nfd = $dom->createElement('nfd');

        $dom->appChild($root, $nfd, 'Adicionando tag Pedido');      

        /* Inscrição Municipal */
        $dom->addChild(
            $nfd,
            'inscricaomunicipalemissor',
            $inscricaoMunicipal,
            false,
            "Inscricao Municipal Emissor",
            false
        );

        /* Inscrição Municipal */
        $dom->addChild(
            $nfd,
            'numeronf',
            $nfseNumero,
            false,
            "numero nf",
            false
        );

        /* Inscrição Municipal */
        $dom->addChild(
            $nfd,
            'motivocancelamento',
            $motivocancelamento,
            false,
            "Motivo Cancelamento",
            false
        );

        /* Inscrição Municipal */
        $dom->addChild(
            $nfd,
            'datacancelamento',
            $datacancelamento,
            false,
            "Data Cancelamento",
            false
        );

        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);
        $this->validar($versao, $body, $this->schemeFolder, $xsd, '');

        return '<?xml version="1.0" encoding="utf-8"?>' . $body;
    }
}
