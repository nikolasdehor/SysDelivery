<?php

namespace App\Libraries;

class PagamentoGateway
{
    private $config;
    private $logger;

    public function __construct()
    {
        $this->config = config('App');
        $this->logger = \Config\Services::logger();
    }

    /**
     * Processa pagamento via cartão de crédito
     */
    public function processarCartaoCredito($dadosCartao, $valor, $pedidoId)
    {
        try {
            // Validações básicas
            if (!$this->validarDadosCartao($dadosCartao)) {
                return [
                    'sucesso' => false,
                    'erro' => 'Dados do cartão inválidos',
                    'codigo_erro' => 'INVALID_CARD_DATA'
                ];
            }

            if ($valor <= 0) {
                return [
                    'sucesso' => false,
                    'erro' => 'Valor inválido',
                    'codigo_erro' => 'INVALID_AMOUNT'
                ];
            }

            // Simula processamento do pagamento
            $transacaoId = $this->gerarTransacaoId();
            
            // Simula diferentes cenários baseado no número do cartão
            $ultimoDigito = substr($dadosCartao['numero'], -1);
            
            if ($ultimoDigito === '1') {
                // Simula cartão recusado
                $this->logTransacao($transacaoId, $pedidoId, 'RECUSADO', $valor);
                return [
                    'sucesso' => false,
                    'erro' => 'Cartão recusado pela operadora',
                    'codigo_erro' => 'CARD_DECLINED',
                    'transacao_id' => $transacaoId
                ];
            }

            if ($ultimoDigito === '2') {
                // Simula saldo insuficiente
                $this->logTransacao($transacaoId, $pedidoId, 'SALDO_INSUFICIENTE', $valor);
                return [
                    'sucesso' => false,
                    'erro' => 'Saldo insuficiente',
                    'codigo_erro' => 'INSUFFICIENT_FUNDS',
                    'transacao_id' => $transacaoId
                ];
            }

            // Simula pagamento aprovado
            $this->logTransacao($transacaoId, $pedidoId, 'APROVADO', $valor);
            
            return [
                'sucesso' => true,
                'transacao_id' => $transacaoId,
                'valor' => $valor,
                'forma_pagamento' => 'cartao_credito',
                'autorizacao' => $this->gerarCodigoAutorizacao(),
                'parcelas' => $dadosCartao['parcelas'] ?? 1
            ];

        } catch (\Exception $e) {
            $this->logger->error('Erro no processamento do pagamento: ' . $e->getMessage());
            return [
                'sucesso' => false,
                'erro' => 'Erro interno no processamento',
                'codigo_erro' => 'INTERNAL_ERROR'
            ];
        }
    }

    /**
     * Processa pagamento via PIX
     */
    public function processarPix($valor, $pedidoId)
    {
        try {
            if ($valor <= 0) {
                return [
                    'sucesso' => false,
                    'erro' => 'Valor inválido',
                    'codigo_erro' => 'INVALID_AMOUNT'
                ];
            }

            $transacaoId = $this->gerarTransacaoId();
            $codigoPix = $this->gerarCodigoPix($valor, $pedidoId);
            $qrCode = $this->gerarQRCodePix($codigoPix);

            $this->logTransacao($transacaoId, $pedidoId, 'PIX_GERADO', $valor);

            return [
                'sucesso' => true,
                'transacao_id' => $transacaoId,
                'valor' => $valor,
                'forma_pagamento' => 'pix',
                'codigo_pix' => $codigoPix,
                'qr_code' => $qrCode,
                'expiracao' => date('Y-m-d H:i:s', strtotime('+30 minutes'))
            ];

        } catch (\Exception $e) {
            $this->logger->error('Erro na geração do PIX: ' . $e->getMessage());
            return [
                'sucesso' => false,
                'erro' => 'Erro interno na geração do PIX',
                'codigo_erro' => 'INTERNAL_ERROR'
            ];
        }
    }

    /**
     * Verifica status do pagamento PIX
     */
    public function verificarStatusPix($transacaoId)
    {
        // Simula verificação de status
        // Em uma implementação real, consultaria a API do banco
        
        $random = rand(1, 10);
        
        if ($random <= 7) {
            // 70% de chance de estar pago
            return [
                'status' => 'PAGO',
                'data_pagamento' => date('Y-m-d H:i:s'),
                'valor_pago' => 0 // Seria o valor real pago
            ];
        } else {
            // 30% de chance de ainda estar pendente
            return [
                'status' => 'PENDENTE',
                'data_pagamento' => null,
                'valor_pago' => 0
            ];
        }
    }

    /**
     * Processa estorno
     */
    public function processarEstorno($transacaoId, $valor, $motivo = '')
    {
        try {
            $estornoId = $this->gerarTransacaoId();
            
            // Simula processamento do estorno
            $this->logTransacao($estornoId, null, 'ESTORNO', $valor, [
                'transacao_original' => $transacaoId,
                'motivo' => $motivo
            ]);

            return [
                'sucesso' => true,
                'estorno_id' => $estornoId,
                'valor_estornado' => $valor,
                'previsao_credito' => date('Y-m-d', strtotime('+5 days'))
            ];

        } catch (\Exception $e) {
            $this->logger->error('Erro no processamento do estorno: ' . $e->getMessage());
            return [
                'sucesso' => false,
                'erro' => 'Erro interno no processamento do estorno',
                'codigo_erro' => 'INTERNAL_ERROR'
            ];
        }
    }

    /**
     * Valida dados do cartão
     */
    private function validarDadosCartao($dados)
    {
        $camposObrigatorios = ['numero', 'nome', 'validade', 'cvv'];
        
        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                return false;
            }
        }

        // Valida número do cartão (algoritmo de Luhn simplificado)
        $numero = preg_replace('/\D/', '', $dados['numero']);
        if (strlen($numero) < 13 || strlen($numero) > 19) {
            return false;
        }

        // Valida CVV
        $cvv = preg_replace('/\D/', '', $dados['cvv']);
        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            return false;
        }

        // Valida validade
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $dados['validade'])) {
            return false;
        }

        return true;
    }

    /**
     * Gera ID único para transação
     */
    private function gerarTransacaoId()
    {
        return 'TXN_' . date('YmdHis') . '_' . rand(1000, 9999);
    }

    /**
     * Gera código de autorização
     */
    private function gerarCodigoAutorizacao()
    {
        return 'AUTH_' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * Gera código PIX
     */
    private function gerarCodigoPix($valor, $pedidoId)
    {
        // Simula geração de código PIX
        $dados = [
            'valor' => $valor,
            'pedido' => $pedidoId,
            'timestamp' => time()
        ];
        
        return base64_encode(json_encode($dados));
    }

    /**
     * Gera QR Code para PIX
     */
    private function gerarQRCodePix($codigoPix)
    {
        // Em uma implementação real, geraria um QR Code real
        // Por simplicidade, retorna uma URL de exemplo
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
    }

    /**
     * Registra log da transação
     */
    private function logTransacao($transacaoId, $pedidoId, $status, $valor, $extras = [])
    {
        $logData = [
            'transacao_id' => $transacaoId,
            'pedido_id' => $pedidoId,
            'status' => $status,
            'valor' => $valor,
            'timestamp' => date('Y-m-d H:i:s'),
            'extras' => $extras
        ];

        $this->logger->info('Transação de pagamento', $logData);
    }

    /**
     * Calcula taxa de processamento
     */
    public function calcularTaxa($valor, $formaPagamento, $parcelas = 1)
    {
        $taxas = [
            'cartao_credito' => [
                1 => 0.0299, // 2.99% à vista
                2 => 0.0399, // 3.99% em 2x
                3 => 0.0499, // 4.99% em 3x ou mais
            ],
            'cartao_debito' => 0.0199, // 1.99%
            'pix' => 0.0099, // 0.99%
            'dinheiro' => 0.0000 // Sem taxa
        ];

        if ($formaPagamento === 'cartao_credito') {
            if ($parcelas === 1) {
                $taxa = $taxas['cartao_credito'][1];
            } elseif ($parcelas === 2) {
                $taxa = $taxas['cartao_credito'][2];
            } else {
                $taxa = $taxas['cartao_credito'][3];
            }
        } else {
            $taxa = $taxas[$formaPagamento] ?? 0;
        }

        return $valor * $taxa;
    }

    /**
     * Obtém formas de pagamento disponíveis
     */
    public function getFormasPagamentoDisponiveis()
    {
        return [
            'cartao_credito' => [
                'nome' => 'Cartão de Crédito',
                'icone' => 'bi-credit-card',
                'parcelas_max' => 12,
                'taxa_minima' => 2.99
            ],
            'cartao_debito' => [
                'nome' => 'Cartão de Débito',
                'icone' => 'bi-credit-card-2-front',
                'parcelas_max' => 1,
                'taxa_minima' => 1.99
            ],
            'pix' => [
                'nome' => 'PIX',
                'icone' => 'bi-qr-code',
                'parcelas_max' => 1,
                'taxa_minima' => 0.99
            ],
            'dinheiro' => [
                'nome' => 'Dinheiro',
                'icone' => 'bi-cash',
                'parcelas_max' => 1,
                'taxa_minima' => 0.00
            ]
        ];
    }
}
