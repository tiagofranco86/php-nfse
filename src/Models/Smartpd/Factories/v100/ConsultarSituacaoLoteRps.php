<?php

namespace NFePHP\NFSe\Models\Smartpd\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Smartpd\Factories\Factory;

class ConsultarSituacaoLoteRps extends Factory
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
        $protocolo
    ) {
        $dom = new Dom('1.0', 'utf-8');
       
        //Adiciona o Cnpj na tag Prestador
        $dom->addChild(
            $dom,
            'inscricaoMunicipal',
            $im,
            true,
            "Inscricao Municipal",
            true
        );
        // //Adiciona a Inscrição Municipal na tag Prestador
        $dom->addChild(
            $dom,
            'recibo',
            $protocolo,
            true,
            "recibo",
            true
        );

        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);
        return $body;
    }
}
