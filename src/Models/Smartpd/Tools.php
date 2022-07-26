<?php

namespace NFePHP\NFSe\Models\Smartpd;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo Smartpd
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Smartpd\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use stdClass;
use NFePHP\NFSe\Common\DateTime;
use NFePHP\NFSe\Common\Tools as ToolsBase;

abstract class Tools extends ToolsBase
{
    protected $schemeFolder = 'Smartpd';
    /**
     * Constructor
     * @param stdClass $config
     * @param \NFePHP\Common\Certificate|null $certificate
     */
    public function __construct(stdClass $config, $certificate = null)
    {
        $this->config = $config;

        //Se o model já possuia  versão não tem necessidade de pegar da configuração
        if (empty($this->versao)) {
            $this->versao = $config->versao;
        }

        $this->remetenteCNPJCPF = $config->cpf;
        $this->remetenteRazao = $config->razaosocial;
        $this->remetenteIM = $config->im;
        $this->remetenteTipoDoc = 1;
        if ($config->cnpj != '') {
            $this->remetenteCNPJCPF = $config->cnpj;
            $this->remetenteTipoDoc = 2;
        }
        $this->certificate = $certificate;
        $this->timezone    = DateTime::tzdBR($config->siglaUF);


        if (empty($this->versao)) {
            throw new \LogicException('Informe a versão do modelo.');
        }
    }

    public function makeXml($rps, $loteId)
    {
        $class = "NFePHP\\NFSe\\Models\\Smartpd\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);

        $fact->setXmlns($this->xmlns);
        $fact->setSchemeFolder($this->schemeFolder);
        $fact->setCodMun($this->config->cmun);
        $fact->setSignAlgorithm($this->algorithm);
        $fact->setTimezone($this->timezone);

        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $loteId,
            [$rps]
        );

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        return $message;
    }


    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Models\\Smartpd\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);

        $fact->setXmlns($this->xmlns);
        $fact->setSchemeFolder($this->schemeFolder);
        $fact->setCodMun($this->config->cmun);
        $fact->setSignAlgorithm($this->algorithm);
        $fact->setTimezone($this->timezone);
        
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $lote,
            $rpss
        );

        // @header ("Content-Disposition: attachment; filename=\"NFSe_Lote.xml\"" );
        // echo $message;
        // exit;
        return $this->sendRequest('', $message);
    }

    public function ConsultarUrlNota(array $dados)
    {
        extract($dados);
        $class = "NFePHP\\NFSe\\Models\\Smartpd\\Factories\\v{$this->versao}\\ConsultarUrlNota";
        $fact = new $class($this->certificate);
        $this->method = 'urlNfd';

        $fact->setXmlns($this->xmlns);
        $message = $fact->render($this->remetenteIM, $codigoMunicipio, $numeroNfse, $serieNfse);

        return $this->sendRequest($this->urlUtil[$this->config->tpAmb], $message);
    }

    public function consultarAtividade($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\Smartpd\\Factories\\v{$this->versao}\\ConsultarAtividades";
        $fact = new $class($this->certificate);
        $this->method = 'consultarAtividades';

        $fact->setXmlns($this->xmlns);
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $protocolo, $this->config->senha);

        return $this->sendRequest('', $message);
    }

    public function consultarSituacaoLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\Smartpd\\Factories\\v{$this->versao}\\ConsultarSituacaoLoteRps";
        $fact = new $class($this->certificate);
        $this->method = 'nfdSaida';

        $fact->setXmlns($this->xmlns);
        $message = $fact->render($this->remetenteIM, $protocolo);

        return $this->sendRequest($this->urlSaida[$this->config->tpAmb], $message);
    }

    public function cancelarNfse($nfseNumero, $motivoCancelamento, $dataCancelamento)
    {
        $class = "NFePHP\\NFSe\\Models\\Smartpd\\Factories\\v{$this->versao}\\CancelarNfse";
        $fact = new $class($this->certificate);

        $this->method = 'nfdEntradaCancelar';
        $fact->xmlns = $this->xmlns;
        $fact->schemeFolder = $this->schemeFolder;
        $fact->codMun = $this->municipioGerador;
        $fact->algorithm = $this->algorithm;

        $message = $fact->render(
            $this->versao,
            $dataCancelamento,
            $motivoCancelamento,
            $this->remetenteIM,
            $nfseNumero
        );

        return $this->sendRequest('', $message);
    }

    protected function sendRequest($url, $message)
    {
        $this->xmlRequest = $message;
        
        if (!$url) {
            $url = $this->urlEntrada[$this->config->tpAmb];
        }

        if (!is_object($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        //@TODO
        $request =
        "<web:{$this->method}>"
        . $message
        . "</web:{$this->method}>";

        $this->params = [
            'soapAction: ""',
            "Host: $url",
            'Content-Type: text/xml; charset=utf-8',
            "Content-length: ".strlen($request)
        ];

        $action = '';

        return $this->soap->send(
            $url,
            $this->method,
            $action,
            $this->soapversion,
            $this->params,
            $this->namespaces,
            $request
        );
    }
}
