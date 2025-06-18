-- Inserir cupons de exemplo para testar o sistema

INSERT INTO `cupons` (
    `cupons_codigo`, 
    `cupons_descricao`, 
    `cupons_tipo`, 
    `cupons_valor`, 
    `cupons_valor_minimo`, 
    `cupons_data_inicio`, 
    `cupons_data_fim`, 
    `cupons_limite_uso`, 
    `cupons_usado`, 
    `cupons_ativo`
) VALUES 
(
    'BEMVINDO10', 
    'Desconto de 10% para novos clientes', 
    'percentual', 
    10.00, 
    50.00, 
    '2025-01-01', 
    '2025-12-31', 
    100, 
    0, 
    1
),
(
    'FRETE5', 
    'R$ 5,00 de desconto no frete', 
    'valor_fixo', 
    5.00, 
    30.00, 
    '2025-01-01', 
    '2025-12-31', 
    NULL, 
    0, 
    1
),
(
    'PROMO15', 
    'Promoção especial - 15% de desconto', 
    'percentual', 
    15.00, 
    100.00, 
    '2025-01-01', 
    '2025-06-30', 
    50, 
    0, 
    1
);
