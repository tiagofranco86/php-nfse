<?php

namespace NFePHP\NFSe\Models\Publica\Factories\v300;

/**
 * Classe para a renderização dos RPS em XML
 * conforme o modelo Publica
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Publica\RenderRPS
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\NFSe\Models\Publica\Factories\Signer;
use NFePHP\NFSe\Models\Publica\Factories\SignerRps;
use NFePHP\NFSe\Models\Publica\RenderRps as RenderRPSBase;
use NFePHP\NFSe\Models\Publica\Rps;

class RenderRps extends RenderRPSBase
{
    public static function toXml(
        $data,
        \DateTimeZone $timezone,
        Certificate $certificate,
        $algorithm = OPENSSL_ALGO_SHA1
    ) {
        self::$certificate = $certificate;
        self::$algorithm = $algorithm;
        self::$timezone = $timezone;
        $xml = '';
        if (is_object($data)) {
            $xml = self::render($data);
        } elseif (is_array($data)) {
            foreach ($data as $rps) {
                $xml .= self::render($rps);
            }
        }

        $xmlSigned = Signer::sign(
            self::$certificate,
            $xml,
            'Rps',
            'Id',
            self::$algorithm,
            [true, false, null, null],
            '',
            false,
            1,
            false

        );

        $xmlSigned = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xmlSigned);

        return $xmlSigned;
    }

    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @return string
     */
    protected static function render(Rps $rps, &$dom, &$parent)
    {
        self::$dom = $dom;
        $root = self::$dom->createElement('Rps');

        $infRPS = self::$dom->createElement("InfRps");
        $infRPS->setAttribute('id', "assinar");

        //Identificação RPS
        $identificacaoRps = self::$dom->createElement('IdentificacaoRps');

        $rps->infDataEmissao->setTimezone(self::$timezone);

        self::$dom->addChild(
            $identificacaoRps,
            'Numero',
            $rps->infNumero,
            true,
            "Numero do RPS",
            false
        );
        self::$dom->addChild(
            $identificacaoRps,
            'Serie',
            $rps->infSerie,
            true,
            "Serie do RPS",
            false
        );
        self::$dom->addChild(
            $identificacaoRps,
            'Tipo',
            $rps->infTipo,
            true,
            "Tipo do RPS",
            false
        );
        self::$dom->appChild($infRPS, $identificacaoRps, 'Adicionando tag IdentificacaoRPS');
        //FIM Identificação RPS

        self::$dom->addChild(
            $infRPS,
            'DataEmissao',
            $rps->infDataEmissao->format('Y-m-d\TH:i:s'),
            true,
            'Data de Emissão do RPS',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'NaturezaOperacao',
            $rps->infNaturezaOperacao,
            true,
            'NaturezaOperacao',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'OptanteSimplesNacional',
            $rps->infOptanteSimplesNacional,
            true,
            'OptanteSimplesNacional',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'IncentivadorCultural',
            $rps->infIncentivadorCultural,
            true,
            'IncentivadorCultural',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'Status',
            $rps->infStatus,
            true,
            'Status',
            false
        );

        //RPS Substituido
        if (!empty($rps->infRpsSubstituido['numero'])) {
            $rpssubs = self::$dom->createElement('RpsSubstituido');
            self::$dom->addChild(
                $rpssubs,
                'Numero',
                $rps->infRpsSubstituido['numero'],
                true,
                'Numero',
                false
            );
            self::$dom->addChild(
                $rpssubs,
                'Serie',
                $rps->infRpsSubstituido['serie'],
                true,
                'Serie',
                false
            );
            self::$dom->addChild(
                $rpssubs,
                'Tipo',
                $rps->infRpsSubstituido['tipo'],
                true,
                'Tipo',
                false
            );
            self::$dom->appChild($infRPS, $rpssubs, 'Adicionando tag RpsSubstituido em infRps');
        }

        /** Serviços **/
        $servico = self::$dom->createElement('Servico');

        //Valores
        $valores = self::$dom->createElement('Valores');
        self::$dom->addChild(
            $valores,
            'ValorServicos',
            $rps->infValorServicos,
            true,
            'ValorServicos',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorDeducoes',
            $rps->infValorDeducoes,
            false,
            'ValorDeducoes',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorPis',
            $rps->infValorPis,
            false,
            'ValorPis',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorCofins',
            $rps->infValorCofins,
            false,
            'ValorCofins',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorInss',
            $rps->infValorInss,
            false,
            'ValorInss',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorIr',
            $rps->infValorIr,
            false,
            'ValorIr',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorCsll',
            $rps->infValorCsll,
            false,
            'ValorCsll',
            false
        );
        self::$dom->addChild(
            $valores,
            'IssRetido',
            $rps->infIssRetido,
            true,
            'IssRetido',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorIss',
            $rps->infValorIss,
            false,
            'ValorIss',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorIssRetido',
            $rps->infValorIssRetido,
            false,
            'ValorIssRetido',
            false
        );
        self::$dom->addChild(
            $valores,
            'OutrasRetencoes',
            $rps->infOutrasRetencoes,
            false,
            'OutrasRetencoes',
            false
        );
        self::$dom->addChild(
            $valores,
            'BaseCalculo',
            $rps->infBaseCalculo,
            false,
            'BaseCalculo',
            false
        );
        self::$dom->addChild(
            $valores,
            'Aliquota',
            $rps->infAliquota,
            false,
            'Aliquota',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorLiquidoNfse',
            $rps->infValorLiquidoNfse,
            false,
            'ValorLiquidoNfse',
            false
        );
        self::$dom->addChild(
            $valores,
            'DescontoIncondicionado',
            $rps->infDescontoIncondicionado,
            false,
            'DescontoIncondicionado',
            false
        );
        self::$dom->addChild(
            $valores,
            'DescontoCondicionado',
            $rps->infDescontoCondicionado,
            false,
            'DescontoCondicionado',
            false
        );
        self::$dom->appChild($servico, $valores, 'Adicionando tag Valores em Servico');
        //FIM Valores
        //IssConstrucaoCivil
        if (!empty($rps->infIssConstrucaoCivil['numero'])) {
            /** IssConstrucaoCivil **/
            $issConstrucaoCivil = self::$dom->createElement('IssConstrucaoCivil');
            $deducoes = self::$dom->createElement('Deducoes');
            self::$dom->addChild(
                $deducoes,
                'Numero',
                $rps->infIssConstrucaoCivil['numero'],
                false,
                'Numero',
                false
            );
            self::$dom->addChild(
                $deducoes,
                'CnpjPrestador',
                $rps->infIssConstrucaoCivil['cnpj'],
                false,
                'CnpjPrestador',
                false
            );
            self::$dom->addChild(
                $deducoes,
                'Valor',
                $rps->infIssConstrucaoCivil['valor'],
                false,
                'Valor',
                false
            );
            self::$dom->addChild(
                $deducoes,
                'ChaveValidacao',
                $rps->infIssConstrucaoCivil['chave'],
                false,
                'ChaveValidacao',
                false
            );
            self::$dom->appChild($issConstrucaoCivil, $deducoes, 'Adicionando tag Deducoes em IssConstrucaoCivil');
            self::$dom->appChild($servico, $issConstrucaoCivil, 'Adicionando tag issConstrucaoCivil em infRPS');
        }
        //IssConstrucaoCivil
        self::$dom->addChild(
            $servico,
            'ItemListaServico',
            $rps->infItemListaServico,
            true,
            'ItemListaServico',
            false
        );
        if (is_array($rps->infDiscriminacao)) {
            #se add detalhes sobre cada item do rps ou nfse
            $discriminacao = "{";
            foreach ($rps->infDiscriminacao as $item) {
                $discriminacao .= "[";
                foreach ($item as $key => $value) {
                    $discriminacao .= "[" . $key .= '=' . $value . "]";
                }
                $discriminacao .= "]";
            }
            $discriminacao .= "}";
        } else {
            $discriminacao = $rps->infDiscriminacao;
        }
        self::$dom->addChild(
            $servico,
            'Discriminacao',
            $discriminacao,
            true,
            'Descrição dos serviços prestados',
            false
        );

        self::$dom->addChild(
            $servico,
            'InformacoesComplementares',
            $rps->infInformacoesComplementares,
            false,
            'Informacoes complementares',
            false
        );
        self::$dom->addChild(
            $servico,
            'CodigoMunicipio',
            $rps->infCodigoMunicipio,
            true,
            'Codigo IBGE do municipio',
            false
        );

        if ($rps->infCodigoPais) {
            self::$dom->addChild(
                $servico,
                'CodigoPais',
                $rps->infCodigoPais,
                false,
                'Codigo do pais',
                false
            );
        }

        self::$dom->appChild($infRPS, $servico, 'Adicionando tag Servico');
        /** FIM Serviços **/

        /** Prestador **/
        $prestador = self::$dom->createElement('Prestador');
        self::$dom->addChild(
            $prestador,
            'Cnpj',
            $rps->infPrestador['cnpjcpf'],
            true,
            'Cnpj do Prestador',
            false
        );
        self::$dom->addChild(
            $prestador,
            'InscricaoMunicipal',
            $rps->infPrestador['im'],
            true,
            'Inscricao municipal do Prestador',
            false
        );

        self::$dom->appChild($infRPS, $prestador, 'Adicionando tag Prestador em infRPS');
        /** FIM Prestador **/

        /** Tomador **/
        if (!empty($rps->infTomador['razao'])) {
            $tomador = self::$dom->createElement('Tomador');

            //Identificação Tomador
            if (!empty($rps->infTomador['cnpjcpf'])) {
                $identificacaoTomador = self::$dom->createElement('IdentificacaoTomador');
                $cpfCnpjTomador = self::$dom->createElement('CpfCnpj');
                if ($rps->infTomador['tipo'] == 2) {
                    self::$dom->addChild(
                        $cpfCnpjTomador,
                        'Cnpj',
                        $rps->infTomador['cnpjcpf'],
                        true,
                        'Tomador CNPJ',
                        false
                    );
                } else {
                    self::$dom->addChild(
                        $cpfCnpjTomador,
                        'Cpf',
                        $rps->infTomador['cnpjcpf'],
                        true,
                        'Tomador CPF',
                        false
                    );
                }
                self::$dom->appChild(
                    $identificacaoTomador,
                    $cpfCnpjTomador,
                    'Adicionando tag CpfCnpj em IdentificacaTomador'
                );

                self::$dom->appChild(
                    $tomador,
                    $identificacaoTomador,
                    'Adicionando tag IdentificacaoTomador em Tomador'
                );
            }

            //Razao Social
            self::$dom->addChild(
                $tomador,
                'RazaoSocial',
                $rps->infTomador['razao'],
                true,
                'RazaoSocial',
                false
            );

            //Endereço
            if (!empty($rps->infTomadorEndereco['end'])) {
                $endereco = self::$dom->createElement('Endereco');
                self::$dom->addChild(
                    $endereco,
                    'Endereco',
                    $rps->infTomadorEndereco['end'],
                    true,
                    'Endereco',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Numero',
                    $rps->infTomadorEndereco['numero'],
                    false,
                    'Numero',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Complemento',
                    $rps->infTomadorEndereco['complemento'],
                    false,
                    'Complemento',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Bairro',
                    $rps->infTomadorEndereco['bairro'],
                    false,
                    'Bairro',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'CodigoMunicipio',
                    $rps->infTomadorEndereco['cmun'],
                    false,
                    'CodigoMunicipio',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Uf',
                    $rps->infTomadorEndereco['uf'],
                    false,
                    'Uf',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Cep',
                    $rps->infTomadorEndereco['cep'],
                    false,
                    'Cep',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'CodigoPais',
                    $rps->infTomadorEndereco['cod_pais'],
                    false,
                    'Codigo do pais',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Municipio',
                    $rps->infTomadorEndereco['nome_municipio'],
                    false,
                    'Nome do municipio',
                    false
                );
                self::$dom->appChild($tomador, $endereco, 'Adicionando tag Endereco em Tomador');
            }

            //Contato
            if ($rps->infTomador['tel'] != '' || $rps->infTomador['email'] != '') {
                $contato = self::$dom->createElement('Contato');
                self::$dom->addChild(
                    $contato,
                    'Telefone',
                    $rps->infTomador['tel'],
                    false,
                    'Telefone Tomador',
                    false
                );
                self::$dom->addChild(
                    $contato,
                    'Email',
                    $rps->infTomador['email'],
                    false,
                    'Email Tomador',
                    false
                );
                self::$dom->appChild($tomador, $contato, 'Adicionando tag Contato em Tomador');
            }
            self::$dom->appChild($infRPS, $tomador, 'Adicionando tag Tomador em infRPS');
        }

        /** FIM Tomador **/

        /** Intermediario Servico **/
        if (!empty($rps->infIntermediario['razao'])) {
            $intermediario = self::$dom->createElement('IntermediarioServico');
            //Razao Social
            self::$dom->addChild(
                $intermediario,
                'RazaoSocial',
                $rps->infIntermediario['razao'],
                true,
                'Razao Intermediario',
                false
            );
            $cpfCnpjIntermediario = self::$dom->createElement('CpfCnpj');
            if ($rps->infIntermediario['tipo'] == 2) {
                self::$dom->addChild(
                    $cpfCnpjIntermediario,
                    'Cnpj',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'Tomador CNPJ',
                    false
                );
            } else {
                self::$dom->addChild(
                    $cpfCnpjIntermediario,
                    'Cpf',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'Tomador CPF',
                    false
                );
            }
            self::$dom->appChild(
                $intermediario,
                $cpfCnpjIntermediario,
                'Adicionando tag CpfCnpj em IdentificacaoTomador'
            );

            self::$dom->addChild(
                $intermediario,
                'InscricaoMunicipal',
                $rps->infIntermediario['im'],
                false,
                'IM Intermediario',
                false
            );
            self::$dom->appChild($infRPS, $intermediario, 'Adicionando tag Intermediario em infRPS');
        }
        /** FIM Intermediario Servico **/

        /** Condição de Pagamento **/
        if (!empty($rps->infCondicaoPagamento['parcelas'])) {
            $condicaoPagamento = self::$dom->createElement('CondicaoPagamento');

            foreach ($rps->infCondicaoPagamento['parcelas'] as $parcela) {
                $parcelaElement = self::$dom->createElement('Parcelas');
                self::$dom->addChild(
                    $parcelaElement,
                    'Condicao',
                    $parcela['condicao'],
                    false,
                    'Condição',
                    false
                );
                self::$dom->addChild(
                    $parcelaElement,
                    'Parcela',
                    $parcela['parcela'],
                    true,
                    'Número da parcela',
                    false
                );
                self::$dom->addChild(
                    $parcelaElement,
                    'Valor',
                    $parcela['valor'],
                    true,
                    'Valor da parcela',
                    false
                );
                self::$dom->addChild(
                    $parcelaElement,
                    'DataVencimento',
                    $parcela['data_vencimento']->format('Y-m-d'),
                    true,
                    'Data de vencimento da parcela',
                    false
                );
                self::$dom->appChild($condicaoPagamento, $parcelaElement, 'Adicionando tag Parcela em CondicaoPagamento');
            }
            self::$dom->appChild($infRPS, $condicaoPagamento, 'Adicionando tag Construcao em infRPS');
        }
        /** FIM Condição de Pagamento **/

        /** Construção Civil **/
        if (!empty($rps->infConstrucaoCivil['obra'])) {
            $construcao = self::$dom->createElement('ConstrucaoCivil');
            self::$dom->addChild(
                $construcao,
                'CodigoObra',
                $rps->infConstrucaoCivil['obra'],
                false,
                'Codigo da Obra',
                false
            );
            self::$dom->addChild(
                $construcao,
                'Art',
                $rps->infConstrucaoCivil['art'],
                true,
                'Art da Obra',
                false
            );
            self::$dom->appChild($infRPS, $construcao, 'Adicionando tag ConstrucaoCivil em infRPS');
        }
        /** FIM Construção Civil **/


        /** Retificacao RPS **/
        if (!empty($rps->infRetificacaoRPS['numero'])) {            
            $retificacaoRps = self::$dom->createElement('Retificacao');
            self::$dom->addChild(
                $retificacaoRps,
                'NumeroRetificado',
                $rps->infRetificacaoRPS['numero'],
                true,
                'Número Rps retificado',
                false
            );

            self::$dom->addChild(
                $retificacaoRps,
                'SerieRetificado',
                $rps->infRetificacaoRPS['serie'],
                true,
                'Série do Rps retificado',
                false
            );

            self::$dom->addChild(
                $retificacaoRps,
                'TipoRpsRetificado',
                $rps->infRetificacaoRPS['tipo'],
                false,
                'Tipo do Rps retificado',
                false
            );

            //Razao Social
            self::$dom->addChild(
                $retificacaoRps,
                'MotivoRetificacao',
                $rps->infRetificacaoRPS['motivo'],
                true,
                'Motivo da retificação',
                false
            );
            self::$dom->appChild($infRPS, $retificacaoRps, 'Adicionando tag RetificacaoRps em infRPS');
        }
        /** FIM Retificacao RPS **/

        self::$dom->appChild($root, $infRPS, 'Adicionando tag infRPS em RPS');
        self::$dom->appChild($parent, $root, 'Adicionando tag RPS na ListaRps');

        return $root;
    }

    public static function appendRps(
        $data,
        \DateTimeZone $timezone,
        Certificate $certificate,
        $algorithm = OPENSSL_ALGO_SHA1,
        &$dom,
        &$parent
    ) {

        self::$certificate = $certificate;
        self::$algorithm = $algorithm;
        self::$timezone = $timezone;

        if (is_object($data)) {
            //Gera a RPS
            $rpsNode = self::render($data, $dom, $parent);
        }
        //Gera o nó com a assinatura
        SignerRps::sign(
            self::$certificate,
            'InfRps',
            'id',
            self::$algorithm,
            [false, false, null, null],
            $dom,
            $rpsNode
        );
    }
}
