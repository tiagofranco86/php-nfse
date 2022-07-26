<?php

namespace NFePHP\NFSe\Models\Smartpd\Factories\v100;

use NFePHP\NFSe\Models\Smartpd\Rps;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Smartpd\Factories\Signer;
use NFePHP\NFSe\Models\Smartpd\Factories\Factory;

class RecepcionarLoteRps extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao 
     * @param $lote
     * @param $rpss
     * @return mixed
     */
    public function render(
        $versao,
        $lote,
        $rpss
    ) {

        $xsd = "entradanfd";
    
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;

        $root = $dom->createElement('tbnfd');

        foreach ($rpss as $rps) {
            self::appendRps($rps, $root);
        }

        //Parse para XML
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML(), $lote);

        $body = Signer::sign(
            $this->certificate,
            $xml,
            'tbnfd',
            '',
            $this->algorithm,
            [false, false, null, null],
            '',
            true
        );
    
        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);
        $this->validar($versao, $body, $this->schemeFolder, $xsd, '', $this->cmun);
        #echo '<pre>'.print_r($body).'</pre>';die;
        return '<?xml version="1.0" encoding="utf-8"?>' . $body;
    }


    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @return string
     */
    protected static function appendRps(Rps $rps, &$dom, $lote)
    {
        $nfd = $dom->createElement("nfd");
    
        $rps->dataemissao->setTimezone(self::$timezone);
        $rps->dataemissaort->setTimezone(self::$timezone);

        $dom->addChild(
            $nfd,
            'numeronfd',
            $rps->numeronfd,
            true,
            "Numero da nota fiscal",
            false
        );

        $dom->addChild(
            $nfd,
            'codseriedocumento',
            $rps->codseriedocumento,
            true,
            "Serie do documento",
            false
        );
        
        $dom->addChild(
            $nfd,
            'codnaturezaoperacao',
            $rps->codnaturezaoperacao,
            true,
            "Código da natureza",
            false
        );
       
        $dom->addChild(
            $nfd,
            'dataemissao',
            $rps->dataemissao->format('dd/mm/YYYY'),
            true,
            'Data de Emissão da nota fiscal',
            false
        );

        $dom->addChild(
            $nfd,
            'inscricaomunicipalemissor',
            $rps->inscricaomunicipalemissor,
            true,
            'Inscricao Municipal Emissor',
            false
        );

        $dom->addChild(
            $nfd,
            'razaotomador',
            $rps->razaotomador,
            true,
            'Razao Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'nomefantasiatomador',
            $rps->nomefantasiatomador,
            true,
            'Nome fantasia Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'enderecotomador',
            $rps->enderecotomador,
            true,
            'Endereco Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'numeroendereco',
            $rps->numeroendereco,
            true,
            'Número Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'cidadetomador',
            $rps->cidadetomador,
            true,
            'Cidade Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'estadotomador',
            $rps->estadotomador,
            true,
            'Estado Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'paistomador',
            $rps->paistomador,
            true,
            'Pais Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'fonetomador',
            $rps->fonetomador,
            true,
            'Fone Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'faxtomador',
            $rps->faxtomador,
            true,
            'Fax Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'ceptomador',
            $rps->ceptomador,
            true,
            'CEP Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'bairrotomador',
            $rps->bairrotomador,
            true,
            'Bairro Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'bairrotomador',
            $rps->bairrotomador,
            true,
            'Bairro Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'emailtomador',
            $rps->emailtomador,
            true,
            'Email Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'tppessoa',
            $rps->tppessoa,
            true,
            'Tipo Pessoa Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'cpfcnpjtomador',
            $rps->cpfcnpjtomador,
            true,
            'CPF/CNPJ Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'inscricaoestadualtomador',
            $rps->inscricaoestadualtomador,
            true,
            'IE Tomador',
            false
        );

        $dom->addChild(
            $nfd,
            'inscricaomunicipaltomador',
            $rps->inscricaomunicipaltomador,
            true,
            'IM Tomador',
            false
        );

        /** Faturas **/
        $tbfatura = $dom->createElement('tbfatura');

        foreach ($rps->faturas as $faturaInf) {
            //fatura
            $fatura = $dom->createElement('fatura');
            $dom->addChild(
                $fatura,
                'numfatura',
                $faturaInf->numfatura,
                true,
                'numfatura',
                false
            );

            $dom->addChild(
                $fatura,
                'vencimentofatura',
                $faturaInf->vencimentofatura->format('mm/dd/YYYY'),
                true,
                'vencimentofatura',
                false
            );

            $dom->addChild(
                $fatura,
                'valorfatura',
                number_format($faturaInf->valorfatura, 2, ',', ''),
                true,
                'valorfatura',
                false
            );

            $dom->appChild($tbfatura, $fatura, 'Adicionando tag fatura em tbfatura');
        }
        /** Serviços **/
        $tbservico = $dom->createElement('tbservico');

        foreach ($rps->servicos as $servicoInf) {
            //servico
            $servico = $dom->createElement('servico');
            $dom->addChild(
                $servico,
                'quantidade',
                $servicoInf->quantidade,
                true,
                'quantidade',
                false
            );

            $dom->addChild(
                $servico,
                'descricao',
                $servicoInf->descricao,
                true,
                'descricao',
                false
            );

            $dom->addChild(
                $servico,
                'codatividade',
                $servicoInf->codatividade,
                true,
                'codatividade',
                false
            );

            $dom->addChild(
                $servico,
                'valorunitario',
                $servicoInf->valorunitario,
                true,
                'valorunitario',
                false
            );

            $dom->addChild(
                $servico,
                'aliquota',
                $servicoInf->aliquota,
                true,
                'aliquota',
                false
            );

            $dom->addChild(
                $servico,
                'impostoretido',
                $servicoInf->impostoretido,
                true,
                'impostoretido',
                false
            );

            $dom->appChild($tbservico, $servico, 'Adicionando tag servico em tbservico');
        }
        
        $dom->addChild(
            $nfd,
            'observacao',
            $rps->observacao,
            true,
            'observacao',
            false
        );
        $dom->addChild(
            $nfd,
            'razaotransportadora',
            $rps->razaotransportadora,
            false,
            'razaotransportadora',
            false
        );
        $dom->addChild(
            $nfd,
            'cpfcnpjtransportadora',
            $rps->cpfcnpjtransportadora,
            false,
            'cpfcnpjtransportadora',
            false
        );
        $dom->addChild(
            $nfd,
            'enderecotransportadora',
            $rps->enderecotransportadora,
            true,
            'enderecotransportadora',
            false
        );

        $dom->addChild(
            $nfd,
            'pis',
            $rps->pis,
            true,
            'pis',
            false
        );
        $dom->addChild(
            $nfd,
            'cofins',
            $rps->cofins,
            false,
            'cofins',
            false
        );
        $dom->addChild(
            $nfd,
            'csll',
            $rps->csll,
            false,
            'csll',
            false
        );
        $dom->addChild(
            $nfd,
            'irrf',
            $rps->irrf,
            true,
            'irrf',
            false
        );

        $dom->addChild(
            $nfd,
            'inss',
            $rps->inss,
            true,
            'inss',
            false
        );
        

        $dom->addChild(
            $nfd,
            'descdeducoesconstrucao',
            $rps->descdeducoesconstrucao,
            false,
            'descdeducoesconstrucao',
            false
        );
        $dom->addChild(
            $nfd,
            'totaldeducoesconstrucao',
            $rps->totaldeducoesconstrucao,
            true,
            'totaldeducoesconstrucao',
            false
        );

        $dom->addChild(
            $nfd,
            'tributadonomunicipio',
            $rps->tributadonomunicipio,
            true,
            'tributadonomunicipio',
            false
        );

        $dom->addChild(
            $nfd,
            'numerort',
            $lote,
            true,
            'Número do lote',
            false
        );
        

        $dom->addChild(
            $nfd,
            'codigoseriert',
            $rps->codigoseriert,
            false,
            'codigoseriert',
            false
        );
        $dom->addChild(
            $nfd,
            'dataemissaort',
            $rps->dataemissaort,
            true,
            'dataemissaort',
            false
        );

        if (!empty($rps->fatorgerador)) {
            $dom->addChild(
                $nfd,
                'fatorgerador',
                $rps->fatorgerador,
                true,
                'fatorgerador',
                false
            );
        }        
        
        return $nfd;
    }
}
