-- OBTER CLIENTES PAGANTES SEM COBRANÇAS LANÇADAS
SELECT
       pt.id,
       planos_correntes.data_inicio_contrato as 'inicio do contrato',
       p.nome_plano as 'plano',
       pt.regime as 'regime',
       pt.nome_pet as 'pet',
       c.nome_cliente as 'tutor',
       c.celular as 'celular',
       planos_correntes.data_encerramento_contrato as 'data_encerramento',
       cb.valor
FROM
     pets pt
       INNER JOIN (
                  SELECT pp.*
                  FROM pets p
                         INNER JOIN (
                                    SELECT MAX(id) as id, id_pet FROM pets_planos
                                    GROUP BY id_pet
                                    ) mp
                         INNER JOIN pets_planos pp ON mp.id = pp.id
                  GROUP BY pp.id_pet
                  ) planos_correntes
       ON planos_correntes.id_pet = pt.id
       INNER JOIN planos p ON p.id = planos_correntes.id_plano
       INNER JOIN clientes c ON c.id = pt.id_cliente
       LEFT JOIN (
         SELECT cobrancas.id_cliente, SUM(valor_original) as valor
         FROM cobrancas
         WHERE cobrancas.competencia = '2020-08' -- Competência de AGOSTO
         GROUP BY cobrancas.id_cliente
       ) cb ON cb.id_cliente = c.id
WHERE
    pt.ativo = 1  AND
    c.ativo = 1
  AND pt.deleted_at IS NULL
  AND c.deleted_at IS NULL
  AND p.deleted_at IS NULL
  AND pt.regime = 'MENSAL' -- Apenas mensais
  AND cb.valor IS NULL -- Sem valor de cobrança lançado
  AND p.id NOT IN (42, 43) -- Plano diferente do plano FREE
  AND c.id_conveniado IS NULL -- Sem convênio
  AND planos_correntes.data_inicio_contrato < '2020-08-31'
GROUP BY pt.id
ORDER BY tutor;

-- OBTER PETS POR PLANO POR CIDADE
SELECT
       pt.id,
       p.nome_plano as 'plano',
       count(p.id) as 'quantidade'
FROM
     pets pt
       INNER JOIN (
                  SELECT pp.*
                  FROM pets p
                         INNER JOIN (
                                    SELECT MAX(id) as id, id_pet FROM pets_planos
                                    GROUP BY id_pet
                                    ) mp
                         INNER JOIN pets_planos pp ON mp.id = pp.id
                  GROUP BY pp.id_pet
                  ) planos_correntes
       ON planos_correntes.id_pet = pt.id
       INNER JOIN planos p ON p.id = planos_correntes.id_plano
       INNER JOIN clientes c ON c.id = pt.id_cliente
WHERE
    pt.ativo = 1  AND
    c.ativo = 1
  AND pt.deleted_at IS NULL
  AND c.deleted_at IS NULL
  AND p.deleted_at IS NULL
  AND c.cidade LIKE '%Campos dos Goytaca%'
GROUP BY p.id

-- FINANCEIRO - Clientes sem cartão

SELECT
    name, email, ref_code id_externo, `hash`
FROM
    customer
WHERE
    `status` = 'A'
        AND payment_type = 'creditcard'
        AND NOT EXISTS( SELECT
            1
        FROM
            creditcard
        WHERE
            customer_id = customer.id)
        AND EXISTS( SELECT
            1
        FROM
            customer_x_product
        WHERE
            customer_id = customer.id
                AND `interval` = 'M'
                AND `status` = 'A')
ORDER BY name;

-- ERP - Clientes sem faturas lançadas na competência
SELECT c.* FROM clientes c
                  INNER JOIN pets p
                    ON p.id_cliente = c.id
                  INNER JOIN pets_planos pp
                    ON pp.id = p.id_pets_planos
WHERE c.ativo = 1
  AND c.id_conveniado IS NULL
  AND c.id NOT IN (
                  SELECT id_cliente FROM cobrancas
                  WHERE cobrancas.competencia = '2020-11'
                  )
  AND p.regime = 'MENSAL'
GROUP BY c.id
HAVING SUM(pp.valor_momento) > 0;

-- ERP - Cobranças X Clientes - Detalhamento mensal (MMR)
SELECT c.id, c.nome_cliente, c.email, c.cpf, c.ativo, c.forma_pagamento, c.dia_vencimento,
       ifnull(janeiro, 0) as janeiro,
       ifnull(fevereiro, 0) as fevereiro,
       ifnull(marco, 0) as marco,
       ifnull(abril, 0) as abril,
       ifnull(maio, 0) as maio,
       ifnull(junho, 0) as junho,
       ifnull(julho, 0) as julho,
       ifnull(agosto, 0) as agosto,
       ifnull(setembro, 0) as setembro,
       ifnull(outubro, 0) as outubro,
       ifnull(novembro, 0) as novembro,
       ifnull(dezembro, 0) as dezembro

FROM clientes c
       LEFT OUTER JOIN (
                       SELECT cj.id_cliente, SUM(cj.valor_original) as janeiro FROM cobrancas cj
                       WHERE cj.competencia = '2020-01' AND cj.cancelada_em IS NULL
                       GROUP BY cj.id_cliente
                       ) cobrancas_janeiro
         ON cobrancas_janeiro.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cf.id_cliente, SUM(cf.valor_original) as fevereiro FROM cobrancas cf
                       WHERE cf.competencia = '2020-02' AND cf.cancelada_em IS NULL
                       GROUP BY cf.id_cliente
                       ) cobrancas_fevereiro
         ON cobrancas_fevereiro.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cm.id_cliente, SUM(cm.valor_original) as marco FROM cobrancas cm
                       WHERE cm.competencia = '2020-03' AND cm.cancelada_em IS NULL
                       GROUP BY cm.id_cliente
                       ) cobrancas_marco
         ON cobrancas_marco.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT ca.id_cliente, SUM(ca.valor_original) as abril FROM cobrancas ca
                       WHERE ca.competencia = '2020-04' AND ca.cancelada_em IS NULL
                       GROUP BY ca.id_cliente
                       ) cobrancas_abril
         ON cobrancas_abril.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cma.id_cliente, SUM(cma.valor_original) as maio FROM cobrancas cma
                       WHERE cma.competencia = '2020-05' AND cma.cancelada_em IS NULL
                       GROUP BY cma.id_cliente
                       ) cobrancas_maio
         ON cobrancas_maio.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cju.id_cliente, SUM(cju.valor_original) as junho FROM cobrancas cju
                       WHERE cju.competencia = '2020-06' AND cju.cancelada_em IS NULL
                       GROUP BY cju.id_cliente
                       ) cobrancas_junho
         ON cobrancas_maio.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cjh.id_cliente, SUM(cjh.valor_original) as julho FROM cobrancas cjh
                       WHERE cjh.competencia = '2020-07' AND cjh.cancelada_em IS NULL
                       GROUP BY cjh.id_cliente
                       ) cobrancas_julho
         ON cobrancas_julho.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cag.id_cliente, SUM(cag.valor_original) as agosto FROM cobrancas cag
                       WHERE cag.competencia = '2020-08' AND cag.cancelada_em IS NULL
                       GROUP BY cag.id_cliente
                       ) cobrancas_agosto
         ON cobrancas_agosto.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cse.id_cliente, SUM(cse.valor_original) as setembro FROM cobrancas cse
                       WHERE cse.competencia = '2020-09' AND cse.cancelada_em IS NULL
                       GROUP BY cse.id_cliente
                       ) cobrancas_setembro
         ON cobrancas_setembro.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cou.id_cliente, SUM(cou.valor_original) as outubro FROM cobrancas cou
                       WHERE cou.competencia = '2020-10' AND cou.cancelada_em IS NULL
                       GROUP BY cou.id_cliente
                       ) cobrancas_outubro
         ON cobrancas_outubro.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cno.id_cliente, SUM(cno.valor_original) as novembro FROM cobrancas cno
                       WHERE cno.competencia = '2020-11' AND cno.cancelada_em IS NULL
                       GROUP BY cno.id_cliente
                       ) cobrancas_novembro
         ON cobrancas_novembro.id_cliente = c.id
       LEFT OUTER JOIN (
                       SELECT cde.id_cliente, SUM(cde.valor_original) as dezembro FROM cobrancas cde
                       WHERE cde.competencia = '2020-12' AND cde.cancelada_em IS NULL
                       GROUP BY cde.id_cliente
                       ) cobrancas_dezembro
         ON cobrancas_dezembro.id_cliente = c.id
GROUP BY c.id
ORDER BY c.nome_cliente ASC

-- ERP - Lifepet Angels
SELECT p.id, p.nome_pet, c.id, c.nome_cliente, c.email, p.regime, p.regime_angel, p.data_angel, p.valor_angel, pl.nome_plano, pl.id, pp.valor_momento, pp.data_inicio_contrato FROM pets p
INNER JOIN clientes c ON c.id = p.id_cliente
INNER JOIN pets_planos pp on p.id_pets_planos = pp.id
INNER JOIN planos pl ON pl.id = pp.id_plano
WHERE p.ativo = 1
AND angel = 1;

-- ERP - Clientes Inadimplentes +60 dias
SELECT cc.id, cc.valor_original, cc.id_cliente, cc.data_vencimento,
       p.pago,
       c.nome_cliente, c.ativo,
       DATEDIFF(cc.data_vencimento, CURRENT_DATE) as dias_vencidos FROM cobrancas cc
        LEFT JOIN (
                  SELECT id_cobranca, SUM(pagamentos.valor_pago) as pago FROM pagamentos
                  GROUP BY id_cobranca
                  HAVING SUM(pagamentos.valor_pago) IS NOT NULL
                  ) p ON p.id_cobranca = cc.id
        INNER JOIN clientes c on cc.id_cliente = c.id
WHERE c.ativo = 1 AND
      c.deleted_at IS NULL AND
      p.pago IS NULL AND
      DATEDIFF(cc.data_vencimento, CURRENT_DATE) < -60 AND
      cc.cancelada_em IS NULL AND
      cc.status = 1 AND
      cc.deleted_at IS NULL
ORDER BY dias_vencidos ASC;

-- APPLIFEPET = Credenciados
SELECT * FROM credenciados

-- APPLIFEPET - Ranking Prestadores
SELECT prestadores.*, IFNULL(h.emissoes, 0) as emissoes
FROM prestadores
       LEFT JOIN (
                 SELECT id_prestador, COUNT(*) as emissoes FROM historico_uso
                 WHERE status = 'LIBERADO'
                   AND deleted_at IS NULL
                   AND DATEDIFF(created_at, CURRENT_DATE) > -90
                 GROUP BY id_prestador) h
         ON prestadores.id = h.id_prestador
WHERE prestadores.deleted_at IS NULL
ORDER BY h.emissoes;

-- APPLIFEPET - Cancelamentos LPT
select COUNT(*) as cancelamentos, DATE_FORMAT(data_encerramento_contrato, '%Y-%m') as competencia, clientes.id as id_cliente
FROM `pets_planos`
       INNER JOIN `pets` ON `pets`.`id` = `pets_planos`.`id_pet`
       INNER JOIN `clientes` on `clientes`.`id` = `pets`.`id_cliente`
WHERE `data_encerramento_contrato` between '2020-09-01' and CURRENT_DATE
  and `pets_planos`.`id_plano` in (61, 59, 58, 56, 55, 52)
  and `clientes`.`ativo` = 0
  and `pets_planos`.`deleted_at` is null
group by DATE_FORMAT(`data_encerramento_contrato`,'%Y-%m'), `clientes`.`id`

-- APPLIFEPET - Planos ativos
SELECT
       h.id,
       planos_correntes.data_inicio_contrato as 'inicio do contrato',
       p.nome_plano as 'plano',
       pt.regime as 'regime',
       pt.nome_pet as 'pet',
       c.nome_cliente as 'tutor',
       c.celular as 'celular',
       c.cidade as 'cidade',
       c.estado as 'estado',
       c.email as ‘email’,
    planos_correntes.data_encerramento_contrato as 'data_encerramento',
    pt.numero_microchip as 'microchip'
    FROM
    pets h
    INNER JOIN (
    SELECT pp.*
    FROM pets p
    INNER JOIN (
    SELECT MAX(id) as id, id_pet FROM pets_planos
    GROUP BY id_pet
    ) mp
    INNER JOIN pets_planos pp ON mp.id = pp.id
    GROUP BY pp.id_pet
    ) planos_correntes ON planos_correntes.id_pet = h.id
    INNER JOIN planos p ON p.id = planos_correntes.id_plano
    INNER JOIN pets pt ON pt.id = h.id
    INNER JOIN clientes c ON c.id = pt.id_cliente
    WHERE
    pt.ativo = 1  AND
    c.ativo = 1
    AND h.deleted_at IS NULL
    AND c.deleted_at IS NULL
    AND p.deleted_at IS NULL
    GROUP BY h.id

-- Applifepet - Participações
SELECT prt.id as 'id_participacao', hst.numero_guia, pts.id as id_pet, pts.nome_pet, pln.nome_plano, clt.id as id_cliente, clt.nome_cliente, prt.valor_participacao, prt.vigencia_inicio, prt.vigencia_fim, prt.competencia, prt.agendado, prt.executado, prt.created_at as 'criado_em'
FROM `participacao` prt
       INNER JOIN historico_uso hst ON hst.id = prt.id_historico_uso
       INNER JOIN clientes clt ON prt.id_cliente = clt.id
       INNER JOIN pets pts ON pts.id = prt.id_pet
       INNER JOIN pets_planos ptp ON ptp.id = pts.id_pets_planos
       INNER JOIN planos pln ON ptp.id_plano = pln.id
WHERE YEAR(prt.created_at) >= 2021
ORDER BY prt.id DESC;