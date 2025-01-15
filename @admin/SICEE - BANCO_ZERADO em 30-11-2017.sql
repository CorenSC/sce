-- phpMyAdmin SQL Dump
-- version 4.1.14.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Tempo de geração: 29/11/2017 às 12:02
-- Versão do servidor: 5.5.33
-- Versão do PHP: 5.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de dados: `coren_sicee`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `acao`
--

CREATE TABLE IF NOT EXISTS `acao` (
  `idacao` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  PRIMARY KEY (`idacao`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Fazendo dump de dados para tabela `acao`
--

INSERT INTO `acao` (`idacao`, `nome`) VALUES(1, 'Efetuou login');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(2, 'Inseriu processo');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(3, 'Atualizou processo');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(4, 'Removeu processo');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(5, 'Inseriu documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(6, 'Atualizou documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(7, 'Removeu documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(8, 'Visualizou documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(9, 'Inseriu usuário');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(10, 'Atualizou usuário');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(11, 'Removeu usuário');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(12, 'Inseriu perfil de usuário');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(13, 'Atualizou perfil de usuário');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(14, 'Removeu perfil de usuário');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(15, 'Inseriu tipo de documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(16, 'Atualizou tipo de documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(17, 'Removeu tipo de documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(18, 'Usuário atualizou seus dados');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(19, 'Enviou e-mail');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(20, 'Inseriu modelo de documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(21, 'Atualizou modelo de documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(22, 'Removeu modelo de documento');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(23, 'Inseriu subseção');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(24, 'Atualizou subseção');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(25, 'Removeu subseção');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(26, 'Inseriu etapa');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(27, 'Atualizou etapa');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(28, 'Removeu etapa');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(29, 'Não enviou e-mail');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(30, 'Atualizou etapa do processo');
INSERT INTO `acao` (`idacao`, `nome`) VALUES(31, 'Recuperou dados de acesso');

-- --------------------------------------------------------

--
-- Estrutura para tabela `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `idconfig` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(10) NOT NULL,
  `dtatualizacao` int(8) NOT NULL DEFAULT '0',
  `descricao` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idconfig`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Fazendo dump de dados para tabela `config`
--

INSERT INTO `config` (`idconfig`, `nome`, `dtatualizacao`, `descricao`) VALUES(1, 'dtfim', 20171129, 'Ultima verificação de necessidade de enviar email as instituições/processos com dtfim > 34 meses');

-- --------------------------------------------------------

--
-- Estrutura para tabela `documento`
--

CREATE TABLE IF NOT EXISTS `documento` (
  `iddocumento` int(11) NOT NULL AUTO_INCREMENT,
  `idprocesso` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `iddocumentotipo` int(11) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `dtcriacao` datetime NOT NULL,
  `dtatualizacao` datetime DEFAULT NULL,
  `obs` text COMMENT 'Observações ao enviar o documento',
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`iddocumento`),
  KEY `fk_documento_usuario1_idx` (`idusuario`),
  KEY `fk_documento_processo1_idx` (`idprocesso`),
  KEY `fk_documento_checklist1_idx` (`iddocumentotipo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentotipo`
--

CREATE TABLE IF NOT EXISTS `documentotipo` (
  `iddocumentotipo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(240) DEFAULT NULL,
  `flag` tinyint(3) unsigned DEFAULT '1',
  PRIMARY KEY (`iddocumentotipo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Fazendo dump de dados para tabela `documentotipo`
--

INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(1, 'Regimento Interno das Comissões de Ética de Enfermagem', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(2, 'Parecer técnico', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(3, 'Relação de nome, inscrição no Coren/SC e categoria dos profissionais de enfermagem', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(4, 'Edital de Convocação para eleição dos integrantes da Comissão de Ética de Enfermagem', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(5, 'Edital de Proclamação dos resultados das eleições para a Comissão de Ética de Enfermagem', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(6, 'Relação dos profissionais de enfermagem aptos ou inaptos a votar ou ser eleitos', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(7, 'Portaria', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(9, 'Termo de posse assinado', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(8, 'Termo de posse', 1);
INSERT INTO `documentotipo` (`iddocumentotipo`, `nome`, `flag`) VALUES(10, 'Parecer técnico homologado', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapa`
--

CREATE TABLE IF NOT EXISTS `etapa` (
  `idetapa` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `ordem` double NOT NULL,
  `fluxo` tinyint(1) NOT NULL COMMENT '0 => faz parte do fluxo principal | 1 => faz parte do fluxo alternativo',
  `aprova` tinyint(1) NOT NULL COMMENT '0 => não precisa aprovar | 1 => aparecer campo para aprovação do documento',
  `msgemail1` text,
  `msgemail2` text,
  `escolhedata` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 => não | 1 => sim',
  `msgadd` text COMMENT 'texto exibido na tela de inserção de documentos',
  `msgcapa` text COMMENT 'texto exibido na capa do processo',
  `bloquear` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 => etapa não bloqueia modificações | 1 => etapa bloqueia modificações',
  `expira` int(3) NOT NULL DEFAULT '0' COMMENT '0 => não expira | 30/60/90/120 => número de dias até o login da instituição expirar',
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idetapa`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Fazendo dump de dados para tabela `etapa`
--

INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(1, 'Abertura do Processo de Implantação/Renovação da CEEn', 'Momento em que o processo é criado no sistema (pela instituição ou pelo Coren/SC). Nesta etapa é obrigatório o envio do documento Regimento Interno elaborado conforme o Modelo I, disponível através do botão azul Modelos de Documentos - disponível na área superior dessa tela', 1, 0, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, '', 'Nesta etapa a Gerência de Enfermagem da instituição acessa o Sistema de Implantação das CEEn no site do Coren/SC. \r\nPara iniciar um processo é obrigatório o envio do Regimento Interno, elaborado conforme o Modelo I.\r\nA partir do inicio do processo pela instituição da saúde, o Sistema do Coren/SC cria um login e senha.\r\n\r\nNesta fase do processo, a Gerência de Enfermagem da instituição de saúde designa por portaria ou outro instrumento administrativo, a Comissão de Regimento Interno da CEEn para estudar e apresentar a proposta de Regimento, tendo como referência o Modelo I (disponível na área de Modelos de Documentos). É aconselhável que os diferentes níveis profissionais da Enfermagem estejam representados na Comissão de Regimento (Enfermeiro, Técnico em Enfermagem e Auxiliar de Enfermagem).\r\nNo Âmbito Institucional:\r\n\r\nA Comissão de Regimento Interno da CEEn informa-se sobre o assunto, tendo como base o presente roteiro, a Decisão Coren/SC 002/2006 e o modelo de Regimento Interno (Modelo I).\r\nDivulga e discute a proposta de Regimento no âmbito da Enfermagem da entidade.\r\nElabora a proposta de Regimento Interno da CEEn da Instituição.\r\nRegistra na última página do regimento o nome completo dos integrantes da Comissão de Regimento (acompanhado do número de registro no Coren/SC) conforme consta no Modelo I.\r\nA Gerência de Enfermagem convoca uma assembleia, incluindo Enfermeiros, Técnicos, Auxiliares e Atendentes de Enfermagem, para apresentação, discussão e aprovação do Regimento Interno, sob a coordenação da Comissão de Regimento Interno da CEEn da entidade.\r\nA aprovação do Regimento Interno pela Enfermagem da entidade será feita constar: no Art. 1º do Regimento – Indicação do nome da Instituição e a data da assembleia. Na última página, faz constar o local e a data, segundo consta no Modelo I.\r\nA Gerência de Enfermagem insere no processo o original do Regimento Interno da CEEn da instituição para ser analisado pela Comissão de Ética do Coren/SC (CEC).', 0, 120, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(2, 'CEC precisa analisar o Regimento Interno', 'A CEC (Comissão de Ética do Coren-SC) analisa o Regimento Interno enviado pela instituição. Se o regimento estiver de acordo com o proposto no Modelo I (em anexo à Decisão Coren-SC 002/2006), na próxima etapa a CEC emitirá parecer técnico de aprovação ad referendum, este que será apreciado e homologado em Reunião Ordinária da CEC e Reunião Ordinária de Plenária.\r\nSe necessitar correções no Regimento Interno, estas serão justificadas em campo especifico no sistema. A justificativa utilizada será enviada para o(s) e-mail(s) cadastrado(s) da instituição (estes e-mails devem ser atualizados sempre que necessário, sendo a área para atualização alcançada através de clique no nome do usuário, em azul, localizado no canto superior direito, ao lado do botão Sair).', 2, 0, 1, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, '', 'Nesta etapa a CEC providencia a análise e a aprovação do Regimento Interno. Se não estiver de acordo com o estabelecido, elabora justificativa com as devidas orientações que são enviadas por e-mail aos endereços cadastrados no sistema pela Gerência de Enfermagem da instituição.\r\nCaso o Regimento Interno não seja aprovado, a Gerência de Enfermagem, junto com os integrantes da Comissão de Regimento, faz as adequações ou alterações e insere o original no processo.', 0, 120, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(3, 'Instituição precisa corrigir o Regimento Interno', 'Nesta etapa a Gerência de Enfermagem da Instituição de Saúde, junto com os integrantes da Comissão de Regimento, faz as adequações ou alterações conforme as orientações da CEC e insere o documento corrigido no processo, dando continuidade ao processo de implantação/renovação da CEEn.', 2.1, 1, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Envie o documento corrigido conforme a justificativa de não aprovação dada pelo Coren/SC.', 'Efetue as modificações no Regimento Interno conforme a justificativa da CEC recebida por e-mail e envie o documento corrigido, clicando no botão cinza Adicionar Documento, localizado na parte superior desta tela.\r\nApós o envio do documento corrigido, a CEC analisará e aprovar ou não o novo regimento enviado, dando continuidade ao processo de implantação/renovação da CEEn.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(8, 'Instituição precisa enviar o Edital de Convocação para as Eleições', 'Com base na Relação dos nomes de todos os profissionais de Enfermagem com vínculo empregatício na entidade aprovada pelo Coren/SC, a Gerência de Enfermagem da instituição insere no processo o Edital de Convocação para as eleições dos membros da CEEn, orientando-se pelo modelo de edital fornecido pelo Coren/SC - Modelo III (disponível através do botão Modelos de Documentos, localizado na área superior desta tela).', 6, 0, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Envie abaixo o documento elaborado conforme o Modelo III, disponível através do botão azul chamado Modelos de Documentos, localizado na área superior da tela.', 'Com base na Relação dos nomes de todos os profissionais de Enfermagem com vínculo empregatício na entidade aprovada pelo Coren/SC, a Gerência de Enfermagem deflagra o processo eleitoral e publica o Edital de Convocação para as eleições dos membros da CEEn, 45 dias antes do pleito, orientando-se pelo modelo de edital fornecido pelo Coren/SC - Modelo III (disponível através do botão Modelos de Documentos, localizado na área superior desta tela).\r\nNo mesmo dia da publicação, a Gerência de Enfermagem insere no processo a Cópia do Edital de Convocação das Eleições. \r\nDesigna paralelamente a Comissão Eleitoral (constituída por representantes dos diferentes níveis profissionais: Enfermeiro, Técnico em Enfermagem e Auxiliar de Enfermagem).  \r\nAs orientações para os Procedimentos da Comissão Eleitoral na instituição de saúde estão descritas no Roteiro para Implantação e Renovação das Comissões de Ética de Enfermagem (CEEn) nas entidades de saúde, disponível na área de Modelos de Documentos - acessível através do botão com este nome localizado na área superior desta tela.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(9, 'Instituição precisa enviar o Edital de Proclamação dos Resultados das Eleições', 'A instituição após realizar as eleições proclama o resultado através do documento Edital de Proclamação dos Resultados das Eleições, elaborado conforme o Modelo IV - disponível na área de Modelos de Documentos.', 7, 0, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Envie abaixo o documento elaborado conforme o Modelo IV - disponível através da área Modelos de Documentos, localizada na parte superior desta tela.', 'A Gerência de Enfermagem oficializa o resultado das eleições através do Edital de Proclamação dos Resultados, segundo o modelo fornecido pelo Coren/SC, em 24 (vinte e quatro) horas após o pleito, contendo:\r\na)	O total de eleitores por nível profissional.\r\nb)	O nome de todos os eleitos por nível profissional e seu respectivo número de inscrição no Coren/SC.\r\nc)	O número de votos que cada candidato recebeu.\r\nd)	O número de votos nulos, votos em branco e o total de votos válidos.\r\ne)	O nome dos membros efetivos e suplentes e o respectivo número de inscrição no Coren/SC.\r\nA Gerência de Enfermagem insere no processo a cópia do Edital de Proclamação dos Resultados das Eleições à CEC.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(10, 'CEC precisa aprovar o Edital de Proclamação dos Resultados das Eleições', 'CEC analisa os resultados e se não estiver de acordo com o estabelecido dará as orientações pertinentes à Gerência de Enfermagem a qual providenciará as adequações. A justificava em caso da não aprovação do Edital será dada por e-mail, por isso a instituição deve manter os e-mails atualizados. (Para atualizar os e-mails ou verificar se eles estão corretos, acesse a área de usuário clicando no botão com texto em azul ao lado do botão Sair)', 8, 0, 1, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, '', 'CEC analisa os resultados e se não estiver de acordo com o estabelecido dará as orientações pertinentes à Gerência de Enfermagem a qual providenciará as adequações. Caso não seja aprovado a justificativa será enviada para os e-mails da instituição cadastrados no sistema.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(12, 'CEC precisa homologar a Portaria', 'A CEC elabora a Portaria designando os membros efetivos e suplentes da CEEn da Instituição de Saúde, encaminha para  a Presidente do Coren/SC assinar e insere o documento no processo.', 9, 0, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Insira o documento Portaria para dar andamento ao processo.', 'Presidente do Coren/SC homologa o resultado das eleições, nomeando a Comissão. Nesta etapa a CEC precisa inserir no processo o documento Portaria homologada pela Presidente. Esta portaria será entregue pela Presidência ou CEC no dia da posse. Os integrantes da CEEn assinarão o Termo de posse durante a solenidade e a partir de então a Comissão está autorizada a iniciar as suas atividades.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(15, 'CEC precisa definir as datas que estará disponível para a cerimônia de posse e enviar o documento Termo de Posse', 'A Comissão de Ética do Coren precisa informar através do sistema as datas e horários disponíveis para realizar a solenidade de Posse da CEEn na instituição e enviar o documento Termo de Posse. Este passo se dá através do botão Adicionar Documento - lá será necessário enviar o documento Termo de Posse e definir as datas e horários disponíveis para a Cerimônia de Posse.', 11, 0, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 1, 'Insira o documento Termo de Posse e escolha as datas de posse disponíveis', 'A CEC precisa informar até três possíveis datas para dar posse à Comissão de Ética de Enfermagem da entidade. Este passo se dá através do botão Adicionar Documento - lá será necessário enviar o documento Termo de Posse e definir as datas e horários disponíveis para a Cerimônia de Posse.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(16, 'Instituição precisa aceitar uma data para posse', 'A instituição aceita ou não uma das datas/horas citadas pela CEC para a solenidade de Posse e programa o cerimonial de posse de acordo com o Modelo V - Cerimonial de Posse da Comissão de Ética de Enfermagem (CEEn) das Instituições de Saúde (disponível através do botão Modelos de Documentos, localizado na área superior desta tela).', 12, 0, 1, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 1, '', 'Escolha abaixo a melhor data/horário (caso haja mais de uma) em que sua instituição está disponível para realizar a cerimônia de posse. Caso não haja disponibilidade em nenhuma data/horário apresentada, selecione que não concorda com nenhuma data e use o campo observação para orientar o Coren outras/datas e horários.', 0, 30, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(17, 'CEC precisa definir uma data e hora para posse', 'Caso a instituição não aceitar a data sugerida pela CEC, a CEC deverá entrar em contato com a instituição para acordarem uma nova data. Após definirem a data, a CEC atualiza o processo - clicando no botão verde, Editar processo - com as informações de data e hora que fora escolhida de comum acordo com a instituição.', 12.1, 1, 0, 'A data e hora da posse está definida! Por favor, comunique o Coren/SC com o máximo de antecedência em caso de indisponibilidade.', 'A data e hora da posse foi definida! Fique atento(a) para não esquecê-la!', 0, '', 'Nesta etapa a CEC/Coren/SC entra em contato com a instituição para definir a data e horário de posse. Após acordarem a data e hora, ela é definida editando este processo e a próxima etapa é iniciada automaticamente.', 0, 30, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(18, 'CEC precisa realizar a Cerimônia de Posse na instituição e enviar o Termo de Posse assinado', 'CEC/Coren/SC realiza a Posse da CEEn na instituição, coleta as assinaturas no Termo de Posse, digitaliza o documento e armazena o mesmo no processo, efetuando o último passo do processo no sistema.', 13, 0, 0, 'Parabéns! O processo foi finalizado no sistema! A partir de hoje, contaremos 2 anos e 10 meses para enviar ao(s) e-mail(s) cadastrado(s) por você um lembrete para renovar sua Comissão de Ética! Até lá!', 'Parabéns! Processo finalizado no sistema!', 0, 'Para finalizar este processo, insira o Termo de Posse assinado.', 'CEC/Coren/SC realiza a Posse da CEEn na instituição, coleta as assinaturas no Termo de Posse, digitaliza o documento e insere o mesmo no processo.', 0, 30, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(19, 'Processo encerrado', 'Processo finalizado!', 14, 0, 0, NULL, NULL, 0, '', 'Processo encerrado. A partir da data em que o documento Termo de posse assinado foi enviado, o sistema aguarda 2 anos e 10 meses e envia um lembrete para o(s) e-mail(s) da instituição cadastrado(s).', 1, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(13, 'Instituição precisa aprovar a Portaria enviada pela CEC', 'Nessa etapa é preciso que a instituição visualize e confira se estão corretos todos os dados apresentados no último documento chamado Portaria enviado pela CEC. Se não aprovar o documento é obrigatório uma justificativa, que será enviada para o e-mail da CEC. Caso seja aprovada o processo avança para a próxima etapa.', 10, 0, 1, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, '', 'A instituição após analisar o documento Portaria emitido pelo Coren/SC aprova ou não o documento. Se aprovar o processo segue para a próxima etapa, caso contrário precisa justificar a não aprovação para que a CEC efetue as modificações necessárias.', 0, 30, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(14, 'CEC precisa corrigir a Portaria', 'Nesta etapa a CEC corrige a portaria a partir das observações dadas pela Instituição e envia um novo documento.', 10.1, 1, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Envie o documento corrigido conforme observações da CEEn.', 'A CEC efetua as correções solicitadas pela instituição de saúde no documento Portaria.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(5, 'Instituição precisa enviar a Relação dos Profissionais de Enfermagem da Instituição', 'Instituição precisa inserir no processo a relação de Nomes, Inscrição e Categoria dos profissionais de Enfermagem, elaborado sobre o arquivo base, Modelo II (disponível na área de Modelos de Documentos) para que, se aprovado pela CEC, dê início ao processo eleitoral da Comissão de Ética.', 4, 0, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Inserir o documento elaborado conforme o Modelo II.', 'Nesta etapa a instituição de saúde precisa efetuar o levantamento dos profissionais de Enfermagem em listagem específica contendo: nome legível, categoria, nº de identidade profissional, CPF e data de admissão na empresa. O documento deve ser elaborado sobre o Modelo II (disponível na área de Modelos de Documentos, botão azul, na área superior desta tela) incluindo nele os dados requisitados e salvando um novo documento que deve ser inserido no processo.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(6, 'Departamento de Fiscalização precisa analisar situação profissional ante ao Coren/SC dos listados pela instituição', 'O Coren/SC ao receber a listagem dos profissionais de Enfermagem da Instituição, faz o levantamento da situação dos profissionais, levando em consideração as determinações do Regimento Interno da CEEn da Instituição.', 5, 0, 1, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Insira a Relação dos profissionais de enfermagem aptos ou inaptos a votar ou ser eleitos, definindo também se a listagem enviada pela instituição está aprovada ou se a instituição precisa fazer modificações na listagem. No segundo caso não aprove o documento para que a instituição possa enviar nova listagem no sistema.', 'Nesta etapa o Enfermeiro Fiscal do Coren/SC da referida área de abrangência analisa a listagem de profissionais e insere no processo o documento que informa à Direção/Gerência de Enfermagem a situação legal de cada um dos membros, informando quem está na condição de elegível e quem pode votar, levando em consideração as determinações do Regimento Interno da CEEn da Instituição, art. 7º: ter no mínimo um ano de efetivo exercício profissional; estar em pleno gozo dos seus direitos profissionais e inexistir condenação pelo Coren/SC em processo ético e processo disciplinar nos últimos cinco anos. Com base no levantamento, insere no processo de implantação/renovação da CEEn, a relação conforme segue:\r\n\r\na) Os que estão aptos para votar e que podem ser eleitos;\r\nb) Os que somente podem votar;\r\nc) Os que estão impossibilitados de votar e de serem eleitos (neste caso, devem procurar o Coren/SC para regularizar a situação).', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(7, 'Instituição precisa corrigir a Relação dos Profissionais de Enfermagem da Instituição', 'Nesta etapa a instituição precisa corrigir a listagem dos profissionais para dar continuidade ao processo.', 5.1, 1, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Envie o documento corrigido conforme o justificativa do Coren/SC e elaborado a partir do Modelo II.', 'Nesta etapa a instituição de saúde precisa efetuar a correção do levantamento dos profissionais de Enfermagem em listagem específica (criada sobre o arquivo Modelo II, disponível na área de Modelos de Documentos) contendo: - nome legível, categoria, nº de identidade profissional, CPF e data de admissão na empresa e inserir ao processo, de modo que, atenda o preconizado pelo Regimento Interno.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(11, 'Instituição precisa corrigir o Edital de Proclamação dos Resultados das Eleições', 'Nesta etapa a instituição precisa corrigir o documento enviado anteriormente com base na justificativa da não aprovação dada pela CEC.', 8.1, 1, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Insira o documento modificado conforme as orientações dadas pela CEC.', 'Efetue as modificações no Edital de Proclamação dos Resultados das Eleições conforme as instruções da CEC e envie o documento corrigido.', 0, 0, 1);
INSERT INTO `etapa` (`idetapa`, `nome`, `descricao`, `ordem`, `fluxo`, `aprova`, `msgemail1`, `msgemail2`, `escolhedata`, `msgadd`, `msgcapa`, `bloquear`, `expira`, `flag`) VALUES(4, 'CEC precisa enviar o Parecer Técnico', 'Neste momento a CEC elabora o documento chamado Parecer Técnico e insere no processo, permitindo que o processo avance para a próxima etapa. O Parecer Técnico possui inicialmente aprovação ad referendum, assim que apreciado e homologado em Reunião Ordinária da CEC e Reunião Ordinária de Plenária, é substituído pelo Parecer Técnico Homologado.', 3, 0, 0, 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 'Use o link abaixo para acessar o processo, verificar qual é a próxima etapa e se é necessária alguma ação sua para dar continuidade ao mesmo.', 0, 'Insira o documento Parecer Técnico (e não se esqueça de posteriormente atualizá-lo pelo Parecer Técnico Homologado).', 'Nesta etapa a CEC precisa adicionar um documento do tipo Parecer Técnico, para que seja permitido avançar para a próxima etapa no processo. É importante que a CEC não esqueça de, quando o documento for homologado, atualizá-lo através do botão Editar, na coluna de ações na linha do documento Parecer Técnico. Ao atualizá-lo, trocar o tipo do documento de Parecer Técnico para Parecer Técnico Homologado.', 0, 0, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapa_documentotipo`
--

CREATE TABLE IF NOT EXISTS `etapa_documentotipo` (
  `idetapa_documentotipo` int(11) NOT NULL AUTO_INCREMENT,
  `idetapa` int(11) NOT NULL,
  `iddocumentotipo` int(11) NOT NULL,
  PRIMARY KEY (`idetapa_documentotipo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

--
-- Fazendo dump de dados para tabela `etapa_documentotipo`
--

INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(19, 1, 1);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(20, 3, 1);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(21, 4, 2);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(36, 5, 3);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(24, 6, 6);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(26, 7, 3);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(37, 8, 4);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(39, 9, 5);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(29, 11, 5);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(30, 12, 7);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(31, 14, 7);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(40, 15, 8);
INSERT INTO `etapa_documentotipo` (`idetapa_documentotipo`, `idetapa`, `iddocumentotipo`) VALUES(34, 18, 9);

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapa_email`
--

CREATE TABLE IF NOT EXISTS `etapa_email` (
  `idetapa_email` int(11) NOT NULL AUTO_INCREMENT,
  `idetapa` int(11) NOT NULL,
  `idperfil` int(11) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `tipoemail` tinyint(1) NOT NULL COMMENT '1 => envia e-mail p/ instituição | 2 => envia e-mail p/ perfil específico | 3 => envia e-mail para usuários específicos',
  `numero` tinyint(1) NOT NULL COMMENT '1 => mensagem de email n. 01 | 2 => mensagem de email n. 02',
  PRIMARY KEY (`idetapa_email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

--
-- Fazendo dump de dados para tabela `etapa_email`
--

INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(16, 1, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(15, 1, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(70, 2, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(69, 2, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(20, 3, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(19, 3, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(22, 4, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(21, 4, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(60, 5, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(59, 5, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(28, 6, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(27, 6, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(31, 7, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(32, 7, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(62, 8, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(61, 8, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(66, 9, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(65, 9, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(37, 10, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(38, 10, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(39, 11, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(40, 11, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(41, 12, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(42, 12, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(43, 13, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(44, 13, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(45, 14, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(46, 14, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(68, 15, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(67, 15, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(49, 16, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(50, 16, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(51, 17, NULL, NULL, 1, 1);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(52, 17, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(56, 18, 4, NULL, 2, 2);
INSERT INTO `etapa_email` (`idetapa_email`, `idetapa`, `idperfil`, `idusuario`, `tipoemail`, `numero`) VALUES(55, 18, NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapa_perfil`
--

CREATE TABLE IF NOT EXISTS `etapa_perfil` (
  `idetapa_perfil` int(10) NOT NULL AUTO_INCREMENT,
  `idetapa` int(10) NOT NULL,
  `idperfil` int(10) NOT NULL,
  PRIMARY KEY (`idetapa_perfil`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Fazendo dump de dados para tabela `etapa_perfil`
--

INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(1, 1, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(31, 2, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(3, 3, 3);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(4, 4, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(26, 5, 3);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(9, 6, 6);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(8, 6, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(11, 7, 3);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(27, 8, 3);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(29, 9, 3);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(14, 10, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(15, 11, 3);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(16, 12, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(17, 13, 3);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(18, 14, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(30, 15, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(20, 16, 3);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(21, 17, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(24, 18, 4);
INSERT INTO `etapa_perfil` (`idetapa_perfil`, `idetapa`, `idperfil`) VALUES(23, 19, 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapa_processo`
--

CREATE TABLE IF NOT EXISTS `etapa_processo` (
  `idetapa_processo` int(11) NOT NULL AUTO_INCREMENT,
  `idetapa` int(11) NOT NULL,
  `idprocesso` int(11) NOT NULL,
  `idusuariocriacao` int(11) NOT NULL COMMENT 'idusuario que iniciou a etapa',
  `idusuarioatualizacao` int(11) DEFAULT NULL COMMENT 'idusuario que atualizou a etapa',
  `dtcriacao` int(8) NOT NULL,
  `dtatualizacao` int(8) DEFAULT NULL,
  `aprovacao` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 => não aprovavel | 1 => aguardando ação | 2 => aprovado | 3 => não aprovado',
  `aprovacaomsg` text,
  PRIMARY KEY (`idetapa_processo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcao`
--

CREATE TABLE IF NOT EXISTS `funcao` (
  `idfuncao` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idfuncao`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Fazendo dump de dados para tabela `funcao`
--

INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(1, 'Adiciona processo', 'Processo', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(2, 'Edita processo', 'Processo', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(3, 'Remove processo', 'Processo', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(4, 'Adiciona documentos', 'Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(5, 'Edita documentos', 'Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(6, 'Remove documentos', 'Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(7, 'Adiciona usuários', 'Usuário', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(8, 'Edita usuários', 'Usuário', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(9, 'Remove usuários', 'Usuário', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(10, 'Adiciona perfis', 'Perfil', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(11, 'Edita perfis', 'Perfil', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(12, 'Remove perfis', 'Perfil', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(13, 'Adiciona subseções', 'Subseção', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(14, 'Edita subseções', 'Subseção', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(15, 'Remove subseções', 'Subseção', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(16, 'Visualiza histórico dos Processos', 'Histórico', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(17, 'Visualiza histórico geral do sistema', 'Histórico', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(18, 'Adiciona modelos de documento', 'Modelo de Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(19, 'Edita modelos de documento', 'Modelo de Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(20, 'Remove modelos de documento', 'Modelo de Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(21, 'Adiciona tipos de documento', 'Tipo de Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(22, 'Edita tipos de documento', 'Tipo de Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(23, 'Remove tipos de documento', 'Tipo de Documento', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(24, 'Adiciona etapas', 'Etapa', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(25, 'Edita etapas', 'Etapa', 1);
INSERT INTO `funcao` (`idfuncao`, `nome`, `categoria`, `flag`) VALUES(26, 'Remove etapas', 'Etapa', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico`
--

CREATE TABLE IF NOT EXISTS `historico` (
  `idhistorico` int(11) NOT NULL AUTO_INCREMENT,
  `dthistorico` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idusuario` int(11) DEFAULT '0',
  `idacao` int(2) NOT NULL COMMENT '1 -> Efetuou Login 2 -> Inseriu processo 3 -> Atualizou processo 4 -> Excluiu processo 5 -> Inseriu documento 6 -> Atualizou documento 7 -> Excluiu documento 8 -> Visualizou documento 9 -> Inseriu usuário 10 -> Atualizou usuário 11 -> Excluiu usuário 12 -> Inseriu perfil 13 -> Atualizou perfil 14 -> Excluiu perfil',
  `idprocesso` int(11) DEFAULT NULL,
  `iddocumento` int(11) DEFAULT NULL,
  `obs` text,
  `ip` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`idhistorico`),
  KEY `data` (`dthistorico`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modelo`
--

CREATE TABLE IF NOT EXISTS `modelo` (
  `idmodelo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(240) DEFAULT NULL,
  `dtcriacao` int(8) NOT NULL,
  `dtatualizacao` int(8) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `flag` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`idmodelo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Fazendo dump de dados para tabela `modelo`
--

INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(1, 'Modelo I - Regimento Interno das Comissões de Ética de Enfermagem (CEEn) das Instituições de Saúde de Santa Catarina', 20171123, NULL, '36f1e67644c7717a29f6ca8f82db7d91.docx', 1);
INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(3, 'Modelo IV - Edital de Proclamação dos resultados das eleições para a Comissão de Ética de Enfermagem (CEEn) das Instituições de Saúde', 20171123, NULL, '4ae9f448c1235360f8bdc47db74309e9.docx', 1);
INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(2, 'Modelo III - Edital de Convocação para as eleições dos integrantes da Comissão de Ética de Enfermagem (CEEn) das Instituições de Saúde', 20171123, NULL, '461af8b51684c56f1debb367410866a4.docx', 1);
INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(4, 'Modelo V - Cerimonial de Posse da Comissão de Ética de Enfermagem (CEEn) das Instituições de Saúde', 20171123, NULL, 'e66615b6f737110f07d2a28ce053496e.docx', 1);
INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(5, 'Outros Modelos - Ata', 20171123, NULL, '657b23db7c65a209e75feae2162c3510.docx', 1);
INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(6, 'Outros Modelos - Planejamento Anual da CEEn das Instituições de Saúde', 20171123, NULL, '480b9f5738beee7c754b4333261623eb.docx', 1);
INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(7, 'Outros Modelos - Relatório Anual das Ações da CEEn das Instituições de Saúde', 20171123, NULL, '55e5601a72af3f4276a1bbfd8a44e4ca.docx', 1);
INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(8, 'Modelo II - Relação de Nome, Inscrição e Categoria dos profissionais de enfermagem', 20171123, NULL, '7456a27c63ddb84d5b4c83d5daa9ccac.xlsx', 1);
INSERT INTO `modelo` (`idmodelo`, `nome`, `dtcriacao`, `dtatualizacao`, `link`, `flag`) VALUES(9, 'Roteiro para Implantação das Comissões de Ética de Enfermagem nas Instituições', 20171123, NULL, '5dbd1ade95ddf4d8c3986700e14a09c6.docx', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `municipio`
--

CREATE TABLE IF NOT EXISTS `municipio` (
  `idmunicipio` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `iduf` int(10) unsigned NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idmunicipio`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=296 ;

--
-- Fazendo dump de dados para tabela `municipio`
--

INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(1, 24, 'Abdon Batista');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(2, 24, 'Abelardo Luz');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(3, 24, 'Agrolândia');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(4, 24, 'Agronômica');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(5, 24, 'Água Doce');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(6, 24, 'Águas de Chapecó');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(7, 24, 'Águas Frias');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(8, 24, 'Águas Mornas');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(9, 24, 'Alfredo Wagner');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(10, 24, 'Alto Bela Vista');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(11, 24, 'Anchieta');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(12, 24, 'Angelina');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(13, 24, 'Anita Garibaldi');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(14, 24, 'Anitápolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(15, 24, 'Antônio Carlos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(16, 24, 'Apiúna');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(17, 24, 'Arabutã');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(18, 24, 'Araquari');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(19, 24, 'Araranguá');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(20, 24, 'Armazém');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(21, 24, 'Arroio Trinta');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(22, 24, 'Arvoredo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(23, 24, 'Ascurra');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(24, 24, 'Atalanta');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(25, 24, 'Aurora');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(26, 24, 'Balneário Arroio do Silva');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(27, 24, 'Balneário Barra do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(28, 24, 'Balneário Camboriú');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(29, 24, 'Balneário Gaivota');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(30, 24, 'Balneário Piçarras');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(31, 24, 'Balneário Rincão');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(32, 24, 'Bandeirante');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(33, 24, 'Barra Bonita');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(34, 24, 'Barra Velha');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(35, 24, 'Bela Vista do Toldo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(36, 24, 'Belmonte');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(37, 24, 'Benedito Novo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(38, 24, 'Biguaçu');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(39, 24, 'Blumenau');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(40, 24, 'Bocaina do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(41, 24, 'Bom Jardim da Serra');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(42, 24, 'Bom Jesus');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(43, 24, 'Bom Jesus do Oeste');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(44, 24, 'Bom Retiro');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(45, 24, 'Bombinhas');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(46, 24, 'Botuverá');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(47, 24, 'Braço do Norte');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(48, 24, 'Braço do Trombudo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(49, 24, 'Brunópolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(50, 24, 'Brusque');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(51, 24, 'Caçador');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(52, 24, 'Caibi');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(53, 24, 'Calmon');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(54, 24, 'Camboriú');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(55, 24, 'Campo Alegre');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(56, 24, 'Campo Belo do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(57, 24, 'Campo Erê');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(58, 24, 'Campos Novos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(59, 24, 'Canelinha');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(60, 24, 'Canoinhas');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(61, 24, 'Capão Alto');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(62, 24, 'Capinzal');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(63, 24, 'Capivari de Baixo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(64, 24, 'Catanduvas');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(65, 24, 'Caxambu do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(66, 24, 'Celso Ramos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(67, 24, 'Cerro Negro');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(68, 24, 'Chapadão do Lageado');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(69, 24, 'Chapecó');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(70, 24, 'Cocal do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(71, 24, 'Concórdia');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(72, 24, 'Cordilheira Alta');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(73, 24, 'Coronel Freitas');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(74, 24, 'Coronel Martins');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(75, 24, 'Correia Pinto');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(76, 24, 'Corupá');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(77, 24, 'Criciúma');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(78, 24, 'Cunha Porã');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(79, 24, 'Cunhataí');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(80, 24, 'Curitibanos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(81, 24, 'Descanso');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(82, 24, 'Dionísio Cerqueira');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(83, 24, 'Dona Emma');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(84, 24, 'Doutor Pedrinho');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(85, 24, 'Entre Rios');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(86, 24, 'Ermo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(87, 24, 'Erval Velho');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(88, 24, 'Faxinal dos Guedes');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(89, 24, 'Flor do Sertão');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(90, 24, 'Florianópolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(91, 24, 'Formosa do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(92, 24, 'Forquilhinha');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(93, 24, 'Fraiburgo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(94, 24, 'Frei Rogério');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(95, 24, 'Galvão');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(96, 24, 'Garopaba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(97, 24, 'Garuva');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(98, 24, 'Gaspar');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(99, 24, 'Governador Celso Ramos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(100, 24, 'Grão Pará');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(101, 24, 'Gravatal');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(102, 24, 'Guabiruba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(103, 24, 'Guaraciaba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(104, 24, 'Guaramirim');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(105, 24, 'Guarujá do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(106, 24, 'Guatambú');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(107, 24, 'Herval d`Oeste');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(108, 24, 'Ibiam');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(109, 24, 'Ibicaré');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(110, 24, 'Ibirama');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(111, 24, 'Içara');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(112, 24, 'Ilhota');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(113, 24, 'Imaruí');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(114, 24, 'Imbituba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(115, 24, 'Imbuia');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(116, 24, 'Indaial');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(117, 24, 'Iomerê');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(118, 24, 'Ipira');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(119, 24, 'Iporã do Oeste');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(120, 24, 'Ipuaçu');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(121, 24, 'Ipumirim');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(122, 24, 'Iraceminha');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(123, 24, 'Irani');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(124, 24, 'Irati');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(125, 24, 'Irineópolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(126, 24, 'Itá');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(127, 24, 'Itaiópolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(128, 24, 'Itajaí');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(129, 24, 'Itapema');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(130, 24, 'Itapiranga');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(131, 24, 'Itapoá');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(132, 24, 'Ituporanga');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(133, 24, 'Jaborá');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(134, 24, 'Jacinto Machado');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(135, 24, 'Jaguaruna');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(136, 24, 'Jaraguá do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(137, 24, 'Jardinópolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(138, 24, 'Joaçaba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(139, 24, 'Joinville');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(140, 24, 'José Boiteux');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(141, 24, 'Jupiá');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(142, 24, 'Lacerdópolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(143, 24, 'Lages');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(144, 24, 'Laguna');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(145, 24, 'Lajeado Grande');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(146, 24, 'Laurentino');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(147, 24, 'Lauro Muller');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(148, 24, 'Lebon Régis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(149, 24, 'Leoberto Leal');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(150, 24, 'Lindóia do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(151, 24, 'Lontras');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(152, 24, 'Luiz Alves');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(153, 24, 'Luzerna');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(154, 24, 'Macieira');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(155, 24, 'Mafra');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(156, 24, 'Major Gercino');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(157, 24, 'Major Vieira');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(158, 24, 'Maracajá');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(159, 24, 'Maravilha');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(160, 24, 'Marema');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(161, 24, 'Massaranduba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(162, 24, 'Matos Costa');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(163, 24, 'Meleiro');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(164, 24, 'Mirim Doce');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(165, 24, 'Modelo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(166, 24, 'Mondaí');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(167, 24, 'Monte Carlo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(168, 24, 'Monte Castelo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(169, 24, 'Morro da Fumaça');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(170, 24, 'Morro Grande');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(171, 24, 'Navegantes');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(172, 24, 'Nova Erechim');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(173, 24, 'Nova Itaberaba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(174, 24, 'Nova Trento');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(175, 24, 'Nova Veneza');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(176, 24, 'Novo Horizonte');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(177, 24, 'Orleans');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(178, 24, 'Otacílio Costa');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(179, 24, 'Ouro');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(180, 24, 'Ouro Verde');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(181, 24, 'Paial');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(182, 24, 'Painel');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(183, 24, 'Palhoça');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(184, 24, 'Palma Sola');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(185, 24, 'Palmeira');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(186, 24, 'Palmitos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(187, 24, 'Papanduva');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(188, 24, 'Paraíso');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(189, 24, 'Passo de Torres');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(190, 24, 'Passos Maia');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(191, 24, 'Paulo Lopes');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(192, 24, 'Pedras Grandes');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(193, 24, 'Penha');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(194, 24, 'Peritiba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(195, 24, 'Pescaria Brava');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(196, 24, 'Petrolândia');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(197, 24, 'Pinhalzinho');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(198, 24, 'Pinheiro Preto');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(199, 24, 'Piratuba');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(200, 24, 'Planalto Alegre');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(201, 24, 'Pomerode');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(202, 24, 'Ponte Alta');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(203, 24, 'Ponte Alta do Norte');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(204, 24, 'Ponte Serrada');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(205, 24, 'Porto Belo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(206, 24, 'Porto União');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(207, 24, 'Pouso Redondo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(208, 24, 'Praia Grande');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(209, 24, 'Presidente Castello Branco');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(210, 24, 'Presidente Getúlio');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(211, 24, 'Presidente Nereu');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(212, 24, 'Princesa');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(213, 24, 'Quilombo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(214, 24, 'Rancho Queimado');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(215, 24, 'Rio das Antas');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(216, 24, 'Rio do Campo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(217, 24, 'Rio do Oeste');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(218, 24, 'Rio do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(219, 24, 'Rio dos Cedros');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(220, 24, 'Rio Fortuna');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(221, 24, 'Rio Negrinho');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(222, 24, 'Rio Rufino');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(223, 24, 'Riqueza');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(224, 24, 'Rodeio');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(225, 24, 'Romelândia');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(226, 24, 'Salete');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(227, 24, 'Saltinho');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(228, 24, 'Salto Veloso');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(229, 24, 'Sangão');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(230, 24, 'Santa Cecília');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(231, 24, 'Santa Helena');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(232, 24, 'Santa Rosa de Lima');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(233, 24, 'Santa Rosa do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(234, 24, 'Santa Terezinha');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(235, 24, 'Santa Terezinha do Progresso');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(236, 24, 'Santiago do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(237, 24, 'Santo Amaro da Imperatriz');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(238, 24, 'São Bento do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(239, 24, 'São Bernardino');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(240, 24, 'São Bonifácio');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(241, 24, 'São Carlos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(242, 24, 'São Cristovão do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(243, 24, 'São Domingos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(244, 24, 'São Francisco do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(245, 24, 'São João Batista');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(246, 24, 'São João do Itaperiú');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(247, 24, 'São João do Oeste');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(248, 24, 'São João do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(249, 24, 'São Joaquim');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(250, 24, 'São José');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(251, 24, 'São José do Cedro');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(252, 24, 'São José do Cerrito');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(253, 24, 'São Lourenço do Oeste');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(254, 24, 'São Ludgero');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(255, 24, 'São Martinho');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(256, 24, 'São Miguel da Boa Vista');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(257, 24, 'São Miguel do Oeste');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(258, 24, 'São Pedro de Alcântara');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(259, 24, 'Saudades');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(260, 24, 'Schroeder');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(261, 24, 'Seara');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(262, 24, 'Serra Alta');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(263, 24, 'Siderópolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(264, 24, 'Sombrio');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(265, 24, 'Sul Brasil');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(266, 24, 'Taió');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(267, 24, 'Tangará');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(268, 24, 'Tigrinhos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(269, 24, 'Tijucas');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(270, 24, 'Timbé do Sul');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(271, 24, 'Timbó');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(272, 24, 'Timbó Grande');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(273, 24, 'Três Barras');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(274, 24, 'Treviso');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(275, 24, 'Treze de Maio');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(276, 24, 'Treze Tílias');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(277, 24, 'Trombudo Central');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(278, 24, 'Tubarão');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(279, 24, 'Tunápolis');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(280, 24, 'Turvo');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(281, 24, 'União do Oeste');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(282, 24, 'Urubici');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(283, 24, 'Urupema');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(284, 24, 'Urussanga');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(285, 24, 'Vargeão');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(286, 24, 'Vargem');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(287, 24, 'Vargem Bonita');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(288, 24, 'Vidal Ramos');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(289, 24, 'Videira');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(290, 24, 'Vitor Meireles');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(291, 24, 'Witmarsum');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(292, 24, 'Xanxerê');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(293, 24, 'Xavantina');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(294, 24, 'Xaxim');
INSERT INTO `municipio` (`idmunicipio`, `iduf`, `nome`) VALUES(295, 24, 'Zortéa');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil`
--

CREATE TABLE IF NOT EXISTS `perfil` (
  `idperfil` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idperfil`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Fazendo dump de dados para tabela `perfil`
--

INSERT INTO `perfil` (`idperfil`, `nome`, `flag`) VALUES(1, 'Administração do Sistema', 1);
INSERT INTO `perfil` (`idperfil`, `nome`, `flag`) VALUES(2, 'Presidente', 1);
INSERT INTO `perfil` (`idperfil`, `nome`, `flag`) VALUES(3, 'Instituição', 1);
INSERT INTO `perfil` (`idperfil`, `nome`, `flag`) VALUES(4, 'Comissão de Ética do Coren/SC', 1);
INSERT INTO `perfil` (`idperfil`, `nome`, `flag`) VALUES(5, 'Secretaria', 1);
INSERT INTO `perfil` (`idperfil`, `nome`, `flag`) VALUES(6, 'Departamento de Fiscalização', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_funcao`
--

CREATE TABLE IF NOT EXISTS `perfil_funcao` (
  `idperfil_funcao` int(11) NOT NULL AUTO_INCREMENT,
  `idperfil` int(11) NOT NULL,
  `idfuncao` int(11) NOT NULL,
  PRIMARY KEY (`idperfil_funcao`),
  KEY `fk_privilegio_funcoes_privilegio1_idx` (`idperfil`),
  KEY `fk_privilegio_funcoes_funcao1_idx` (`idfuncao`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=401 ;

--
-- Fazendo dump de dados para tabela `perfil_funcao`
--

INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(219, 1, 9);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(218, 1, 8);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(217, 1, 7);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(216, 1, 23);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(215, 1, 22);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(214, 1, 21);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(213, 1, 15);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(212, 1, 14);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(211, 1, 13);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(210, 1, 3);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(209, 1, 2);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(208, 1, 1);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(207, 1, 12);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(206, 1, 11);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(205, 1, 10);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(204, 1, 20);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(203, 1, 19);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(202, 1, 18);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(201, 1, 17);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(200, 1, 16);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(199, 1, 26);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(198, 1, 25);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(197, 1, 24);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(384, 4, 9);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(383, 4, 8);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(382, 4, 7);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(381, 4, 23);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(380, 4, 22);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(379, 4, 21);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(378, 4, 15);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(377, 4, 14);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(376, 4, 13);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(375, 4, 3);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(374, 4, 2);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(373, 4, 1);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(372, 4, 12);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(371, 4, 11);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(370, 4, 10);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(369, 4, 20);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(368, 4, 19);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(367, 4, 18);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(366, 4, 17);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(365, 4, 16);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(364, 4, 26);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(363, 4, 25);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(362, 4, 24);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(225, 3, 5);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(224, 3, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(188, 2, 23);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(187, 2, 22);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(186, 2, 21);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(185, 2, 15);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(184, 2, 14);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(183, 2, 13);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(182, 2, 3);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(181, 2, 2);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(180, 2, 1);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(179, 2, 12);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(178, 2, 11);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(177, 2, 10);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(176, 2, 20);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(175, 2, 19);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(174, 2, 18);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(173, 2, 17);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(172, 2, 16);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(171, 2, 26);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(170, 2, 25);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(169, 2, 24);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(168, 2, 6);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(167, 2, 5);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(166, 2, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(391, 6, 16);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(390, 6, 5);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(389, 6, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(400, 5, 17);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(399, 5, 16);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(398, 5, 5);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(322, 8, 9);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(196, 1, 6);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(195, 1, 5);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(194, 1, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(361, 4, 6);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(360, 4, 5);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(359, 4, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(189, 2, 7);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(190, 2, 8);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(191, 2, 9);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(318, 7, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(321, 8, 7);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(329, 9, 6);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(320, 8, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(328, 9, 5);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(327, 9, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(358, 11, 6);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(357, 11, 5);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(356, 11, 4);
INSERT INTO `perfil_funcao` (`idperfil_funcao`, `idperfil`, `idfuncao`) VALUES(397, 5, 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `processo`
--

CREATE TABLE IF NOT EXISTS `processo` (
  `idprocesso` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) DEFAULT NULL,
  `idprocessotipo` int(11) NOT NULL,
  `idetapa` int(11) NOT NULL,
  `numero` int(4) unsigned zerofill NOT NULL,
  `dtcriacao` datetime DEFAULT NULL,
  `dtatualizacao` datetime DEFAULT NULL,
  `dtposse1` datetime DEFAULT NULL,
  `dtposse2` datetime DEFAULT NULL,
  `dtposse3` datetime DEFAULT NULL,
  `obsposse` text,
  `dtescolhida` datetime DEFAULT NULL COMMENT 'Até ter uma data escolhida para posse este campo fica NULO',
  `dtfim` int(8) DEFAULT NULL COMMENT 'Quando o último documento é inserido no processo, armazena-se a data atual + 2 anos e 10 meses. Quando a data atual superar esta armazenada, é disparado um e-mail avisando para renovar a CEEn',
  `dtaviso` datetime DEFAULT NULL COMMENT 'Esta coluna ficará NULA até que o e-mail seja disparado avisando que o usuário deve fazer a renovação da comissão de ética',
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idprocesso`),
  KEY `fk_processo_usuario1_idx` (`idusuario`),
  KEY `fk_processo_tipo1_idx` (`idprocessotipo`),
  KEY `fk_processo_status1_idx` (`idetapa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `processotipo`
--

CREATE TABLE IF NOT EXISTS `processotipo` (
  `idprocessotipo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) NOT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idprocessotipo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Fazendo dump de dados para tabela `processotipo`
--

INSERT INTO `processotipo` (`idprocessotipo`, `nome`, `flag`) VALUES(1, 'Implantação', 1);
INSERT INTO `processotipo` (`idprocessotipo`, `nome`, `flag`) VALUES(2, 'Renovação', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `subsecao`
--

CREATE TABLE IF NOT EXISTS `subsecao` (
  `idsubsecao` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idmunicipio` int(10) unsigned NOT NULL,
  `nome` varchar(200) NOT NULL,
  `flag` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`idsubsecao`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Fazendo dump de dados para tabela `subsecao`
--

INSERT INTO `subsecao` (`idsubsecao`, `idmunicipio`, `nome`, `flag`) VALUES(1, 90, 'Sede', 1);
INSERT INTO `subsecao` (`idsubsecao`, `idmunicipio`, `nome`, `flag`) VALUES(2, 69, '1ª Subseção', 1);
INSERT INTO `subsecao` (`idsubsecao`, `idmunicipio`, `nome`, `flag`) VALUES(3, 77, '3ª Subseção', 1);
INSERT INTO `subsecao` (`idsubsecao`, `idmunicipio`, `nome`, `flag`) VALUES(4, 39, '4ª Subseção', 1);
INSERT INTO `subsecao` (`idsubsecao`, `idmunicipio`, `nome`, `flag`) VALUES(5, 51, '5ª Subseção', 1);
INSERT INTO `subsecao` (`idsubsecao`, `idmunicipio`, `nome`, `flag`) VALUES(6, 139, '6ª Subseção', 1);
INSERT INTO `subsecao` (`idsubsecao`, `idmunicipio`, `nome`, `flag`) VALUES(7, 143, '7ª Subseção', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `subsecao_municipio`
--

CREATE TABLE IF NOT EXISTS `subsecao_municipio` (
  `idsubsecao` int(10) NOT NULL,
  `idmunicipio` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `subsecao_municipio`
--

INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 69);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 286);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 7);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 6);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 2);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 8);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 9);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 12);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 14);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 15);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 38);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 45);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 59);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 90);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 96);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 99);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 129);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 149);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 156);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 174);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 183);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 191);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 205);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 214);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 237);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 240);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 245);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 250);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 258);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(1, 269);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 10);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 11);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 17);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 22);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 32);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 33);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 36);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 42);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 43);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 52);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 57);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 65);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 71);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 72);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 73);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 74);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 78);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 79);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 81);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 82);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 85);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 88);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 89);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 91);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 95);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 103);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 105);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 106);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 118);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 119);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 120);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 121);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 122);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 123);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 124);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 126);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 130);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 133);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 137);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 141);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 145);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 150);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 159);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 160);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 165);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 166);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 172);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 173);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 176);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 180);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 181);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 184);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 186);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 188);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 190);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 194);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 197);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 199);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 200);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 204);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 209);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 212);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 213);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 223);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 225);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 227);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 231);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 235);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 236);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 239);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 241);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 243);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 247);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 251);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 253);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 256);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 257);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 259);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 261);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 262);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 265);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 268);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 279);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 281);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 285);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 292);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 293);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(2, 294);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 283);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 295);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 289);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 287);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 276);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 273);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 272);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 267);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 230);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 228);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 215);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 206);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 203);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 198);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 187);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 179);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 168);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 167);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 162);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 157);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 154);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 153);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 148);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 142);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 138);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 125);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 117);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 109);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 108);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 107);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 94);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 93);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 87);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 64);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 62);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 60);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 58);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 53);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 51);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 49);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 35);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 21);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(5, 5);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 18);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 27);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 30);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 34);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 55);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 76);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 97);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 104);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 127);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 131);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 136);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 139);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 152);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 155);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 161);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 193);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 221);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 238);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 244);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 246);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(6, 260);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 282);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 252);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 249);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 242);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 222);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 202);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 185);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 182);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 178);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 80);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 75);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 67);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 66);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 61);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 56);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 44);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 41);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 40);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 13);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 1);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 284);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 280);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 278);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 275);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 274);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 270);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 264);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 263);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 255);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 254);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 248);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 233);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 232);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 229);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 220);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 208);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 192);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 189);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 177);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 175);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 170);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 169);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 163);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 158);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 147);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 144);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 135);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 134);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 114);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 113);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 111);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 101);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 100);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 92);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 86);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 77);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 70);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 63);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 47);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 29);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 26);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 20);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 19);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 3);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 4);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 16);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 23);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 24);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 25);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 28);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 37);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 39);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 46);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 48);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 50);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 54);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 68);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 83);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 84);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 98);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 102);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 110);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 112);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 115);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 116);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 128);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 132);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 140);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 146);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 151);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 164);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 171);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 196);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 201);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 207);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 210);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 211);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 216);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 217);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 218);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 219);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 224);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 226);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 234);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 266);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 271);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 277);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 288);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 290);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(4, 291);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(7, 143);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 31);
INSERT INTO `subsecao_municipio` (`idsubsecao`, `idmunicipio`) VALUES(3, 195);

-- --------------------------------------------------------

--
-- Estrutura para tabela `uf`
--

CREATE TABLE IF NOT EXISTS `uf` (
  `iduf` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sigla` varchar(2) DEFAULT NULL,
  `nome` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`iduf`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Fazendo dump de dados para tabela `uf`
--

INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(1, 'AC', 'Acre');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(2, 'AL', 'Alagoas');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(3, 'AP', 'Amapá');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(4, 'AM', 'Amazonas');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(5, 'BA', 'Bahia');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(6, 'CE', 'Ceará');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(7, 'DF', 'Distrito Federal');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(8, 'ES', 'Espírito Santo');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(9, 'GO', 'Goiás');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(10, 'MA', 'Maranhão');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(11, 'MT', 'Mato Grosso');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(12, 'MS', 'Mato Grosso do Sul');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(13, 'MG', 'Minas Gerais');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(14, 'PA', 'Pará');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(15, 'PB', 'Paraíba');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(16, 'PR', 'Paraná');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(17, 'PE', 'Pernambuco');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(18, 'PI', 'Piauí');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(19, 'RJ', 'Rio de Janeiro');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(20, 'RN', 'Rio Grande do Norte');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(21, 'RS', 'Rio Grande do Sul');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(22, 'RO', 'Rondônia');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(23, 'RR', 'Roraima');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(24, 'SC', 'Santa Catarina');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(25, 'SP', 'São Paulo');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(26, 'SE', 'Sergipe');
INSERT INTO `uf` (`iduf`, `sigla`, `nome`) VALUES(27, 'TO', 'Tocantins');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `idperfil` int(11) NOT NULL,
  `idmunicipio` int(11) NOT NULL,
  `idsubsecao` int(11) DEFAULT NULL,
  `nome` varchar(200) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(50) NOT NULL,
  `email1` varchar(255) DEFAULT NULL,
  `email2` varchar(255) DEFAULT NULL,
  `celular` varchar(11) DEFAULT NULL,
  `telefone` varchar(11) DEFAULT NULL,
  `nome_instituicao` varchar(200) DEFAULT NULL,
  `dtcriacao` int(8) NOT NULL,
  `dtexpiracao` int(8) NOT NULL DEFAULT '0',
  `tentativas_num` tinyint(1) NOT NULL DEFAULT '0',
  `tentativas_time` int(4) DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `numlosts` tinyint(2) DEFAULT '0' COMMENT 'Número de vezes que pediu para recuperar a senha',
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idusuario`),
  UNIQUE KEY `login_UNIQUE` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Fazendo dump de dados para tabela `usuario`
--

INSERT INTO `usuario` (`idusuario`, `idperfil`, `idmunicipio`, `idsubsecao`, `nome`, `login`, `senha`, `email1`, `email2`, `celular`, `telefone`, `nome_instituicao`, `dtcriacao`, `dtexpiracao`, `tentativas_num`, `tentativas_time`, `lastlogin`, `numlosts`, `flag`) VALUES(1, 1, 1, 1, 'Alexandre Prazeres', 'alexandre', 'b820825cef06ca910616546e19a7b819', 'alexandre.prazeres@corensc.gov.br', NULL, '48991913334', '4832249091', NULL, 20161026, 0, 0, NULL, '2017-11-29 09:42:19', 0, 1);
INSERT INTO `usuario` (`idusuario`, `idperfil`, `idmunicipio`, `idsubsecao`, `nome`, `login`, `senha`, `email1`, `email2`, `celular`, `telefone`, `nome_instituicao`, `dtcriacao`, `dtexpiracao`, `tentativas_num`, `tentativas_time`, `lastlogin`, `numlosts`, `flag`) VALUES(2, 4, 39, 4, 'Maria do Carmo Vicensi', 'mariadocarmo', '6d47f15ff74d8809a3972d534e0c2a51', 'mariadocarmovicensi@gmail.com', NULL, '4991090566', '4832249091', '', 20161026, 0, 0, NULL, '2017-11-10 10:49:50', 0, 1);
INSERT INTO `usuario` (`idusuario`, `idperfil`, `idmunicipio`, `idsubsecao`, `nome`, `login`, `senha`, `email1`, `email2`, `celular`, `telefone`, `nome_instituicao`, `dtcriacao`, `dtexpiracao`, `tentativas_num`, `tentativas_time`, `lastlogin`, `numlosts`, `flag`) VALUES(3, 4, 1, 7, 'Giana Marlize Boeira Poetini', 'giana', '7d3262336f07e0c72c27056c6510b0bf', 'giana.poetini@corensc.gov.br', NULL, '4899999999', '4832249091', '', 20161026, 0, 0, NULL, '2017-11-22 16:47:30', 0, 1);
INSERT INTO `usuario` (`idusuario`, `idperfil`, `idmunicipio`, `idsubsecao`, `nome`, `login`, `senha`, `email1`, `email2`, `celular`, `telefone`, `nome_instituicao`, `dtcriacao`, `dtexpiracao`, `tentativas_num`, `tentativas_time`, `lastlogin`, `numlosts`, `flag`) VALUES(4, 4, 1, 1, 'Maristela Jeci dos Santos', 'maristela', '74a8f8e4e1f8f6888de39c11eff382f5', 'a@a.com', NULL, '4899999999', '4832249091', '', 20161026, 0, 0, NULL, NULL, 0, 1);
INSERT INTO `usuario` (`idusuario`, `idperfil`, `idmunicipio`, `idsubsecao`, `nome`, `login`, `senha`, `email1`, `email2`, `celular`, `telefone`, `nome_instituicao`, `dtcriacao`, `dtexpiracao`, `tentativas_num`, `tentativas_time`, `lastlogin`, `numlosts`, `flag`) VALUES(5, 4, 1, 7, 'Elizandra Faria Andrade', 'elizandra', '1c41c28283b0a6ad3668e45273dde026', 'a@a.com', NULL, '4899999999', '4832249091', NULL, 20161026, 0, 0, NULL, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_processo`
--

CREATE TABLE IF NOT EXISTS `usuario_processo` (
  `idusuario_processo` int(11) NOT NULL AUTO_INCREMENT,
  `idprocesso` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idusuario_processo`),
  KEY `fk_usuario_processo_processo1_idx` (`idprocesso`),
  KEY `fk_usuario_processo_usuario1_idx` (`idusuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
