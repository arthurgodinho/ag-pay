<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'termos-uso',
                'title' => 'Termos de Uso',
                'content' => 'TERMOS E CONDIÇÕES DE USO DO PagueMax

1. ACEITAÇÃO DOS TERMOS

Ao acessar e utilizar os serviços da plataforma PagueMax, você concorda em cumprir e estar vinculado aos seguintes Termos e Condições de Uso. Se você não concorda com qualquer parte destes termos, não deve utilizar nossos serviços.

2. DEFINIÇÕES

2.1. "Plataforma" refere-se ao PagueMax, sistema de gateway de pagamentos.
2.2. "Usuário" refere-se a qualquer pessoa física ou jurídica que utiliza os serviços da Plataforma.
2.3. "Serviços" refere-se a todos os serviços oferecidos pela Plataforma, incluindo processamento de pagamentos, gestão financeira e demais funcionalidades.

3. CADASTRO E CONTA

3.1. Para utilizar os serviços, o Usuário deve criar uma conta fornecendo informações verdadeiras, precisas e completas.
3.2. O Usuário é responsável por manter a confidencialidade de suas credenciais de acesso.
3.3. O Usuário deve notificar imediatamente a Plataforma sobre qualquer uso não autorizado de sua conta.

4. OBRIGAÇÕES DO USUÁRIO

4.1. Utilizar os serviços apenas para fins legais e de acordo com a legislação vigente.
4.2. Não utilizar os serviços para atividades fraudulentas, ilegais ou que violem direitos de terceiros.
4.3. Fornecer informações precisas e atualizadas.
4.4. Manter a segurança de suas credenciais de acesso.

5. TAXAS E PAGAMENTOS

5.1. A Plataforma cobra taxas pelos serviços prestados, conforme divulgado no momento da contratação.
5.2. As taxas podem ser alteradas mediante aviso prévio de 30 dias.
5.3. O Usuário concorda em pagar todas as taxas aplicáveis aos serviços utilizados.

6. LIMITAÇÃO DE RESPONSABILIDADE

6.1. A Plataforma não se responsabiliza por perdas ou danos decorrentes do uso ou impossibilidade de uso dos serviços.
6.2. A Plataforma não garante que os serviços estarão sempre disponíveis, ininterruptos ou livres de erros.

7. PROPRIEDADE INTELECTUAL

7.1. Todo o conteúdo da Plataforma, incluindo textos, gráficos, logos e software, é propriedade da PagueMax.
7.2. O Usuário não pode reproduzir, distribuir ou criar obras derivadas sem autorização prévia.

8. PRIVACIDADE

8.1. O tratamento de dados pessoais está sujeito à nossa Política de Privacidade.
8.2. A Plataforma coleta e processa dados pessoais conforme necessário para prestação dos serviços.

9. RESCISÃO

9.1. A Plataforma pode suspender ou encerrar a conta do Usuário em caso de violação destes Termos.
9.2. O Usuário pode encerrar sua conta a qualquer momento, mediante solicitação.

10. ALTERAÇÕES DOS TERMOS

10.1. A Plataforma reserva-se o direito de modificar estes Termos a qualquer momento.
10.2. Alterações significativas serão comunicadas aos Usuários com antecedência mínima de 30 dias.

11. LEI APLICÁVEL

11.1. Estes Termos são regidos pela legislação brasileira.
11.2. Qualquer disputa será resolvida no foro da comarca de São Paulo, SP.

12. CONTATO

Para questões sobre estes Termos, entre em contato através do suporte da Plataforma.

Última atualização: ' . date('d/m/Y'),
                'is_active' => true,
            ],
            [
                'slug' => 'privacidade',
                'title' => 'Política de Privacidade',
                'content' => 'POLÍTICA DE PRIVACIDADE DO PagueMax

1. INTRODUÇÃO

A PagueMax está comprometida com a proteção da privacidade e dos dados pessoais de seus usuários. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e protegemos suas informações pessoais.

2. DADOS COLETADOS

2.1. Dados fornecidos pelo usuário:
- Nome completo
- CPF ou CNPJ
- Data de nascimento
- Endereço de e-mail
- Número de telefone
- Endereço residencial/comercial
- Dados bancários (quando necessário)
- Documentos de identificação (KYC)

2.2. Dados coletados automaticamente:
- Endereço IP
- Informações do dispositivo
- Dados de navegação
- Cookies e tecnologias similares

3. FINALIDADE DO USO DOS DADOS

Utilizamos seus dados pessoais para:
- Prestação dos serviços de gateway de pagamentos
- Verificação de identidade (KYC)
- Processamento de transações
- Comunicação com o usuário
- Cumprimento de obrigações legais
- Prevenção de fraudes e lavagem de dinheiro
- Melhoria dos serviços

4. COMPARTILHAMENTO DE DADOS

4.1. Compartilhamos dados apenas quando necessário para:
- Prestação dos serviços (adquirentes, processadores de pagamento)
- Cumprimento de obrigações legais
- Prevenção de fraudes
- Com seu consentimento expresso

4.2. Não vendemos seus dados pessoais a terceiros.

5. SEGURANÇA DOS DADOS

5.1. Implementamos medidas técnicas e organizacionais para proteger seus dados.
5.2. Utilizamos criptografia, controles de acesso e monitoramento de segurança.
5.3. Realizamos backups regulares dos dados.

6. RETENÇÃO DE DADOS

6.1. Mantemos seus dados pelo tempo necessário para:
- Prestação dos serviços
- Cumprimento de obrigações legais
- Resolução de disputas
- Prevenção de fraudes

7. DIREITOS DO USUÁRIO

Você tem direito a:
- Acesso aos seus dados pessoais
- Correção de dados inexatos
- Exclusão de dados (quando aplicável)
- Portabilidade dos dados
- Revogação do consentimento
- Oposição ao tratamento

8. COOKIES

8.1. Utilizamos cookies para melhorar sua experiência.
8.2. Você pode gerenciar as preferências de cookies nas configurações do navegador.

9. ALTERAÇÕES NA POLÍTICA

9.1. Podemos atualizar esta Política periodicamente.
9.2. Alterações significativas serão comunicadas aos usuários.

10. LGPD

10.1. Esta Política está em conformidade com a Lei Geral de Proteção de Dados (LGPD - Lei 13.709/2018).
10.2. Temos um Encarregado de Proteção de Dados (DPO) para questões relacionadas à privacidade.

11. CONTATO

Para exercer seus direitos ou esclarecer dúvidas sobre privacidade, entre em contato:
- E-mail: privacidade@seudominio.com
- Através do suporte da Plataforma

Última atualização: ' . date('d/m/Y'),
                'is_active' => true,
            ],
            [
                'slug' => 'pld',
                'title' => 'Prevenção à Lavagem de Dinheiro (PLD)',
                'content' => 'POLÍTICA DE PREVENÇÃO À LAVAGEM DE DINHEIRO (PLD)

1. COMPROMISSO

A PagueMax está comprometida com a prevenção à lavagem de dinheiro e ao financiamento do terrorismo, em conformidade com a legislação brasileira, especialmente a Lei nº 9.613/1998 e suas alterações.

2. OBRIGAÇÕES LEGAIS

2.1. A PagueMax está sujeita às obrigações previstas na legislação de PLD/CFT.
2.2. Realizamos o monitoramento contínuo de transações suspeitas.
2.3. Mantemos registros de todas as operações conforme exigido por lei.

3. IDENTIFICAÇÃO E VERIFICAÇÃO (KYC)

3.1. Todos os usuários devem passar por processo de identificação e verificação (KYC).
3.2. Solicitamos e verificamos documentos de identificação.
3.3. Mantemos cadastro atualizado de todos os usuários.

4. MONITORAMENTO DE TRANSAÇÕES

4.1. Monitoramos todas as transações em busca de padrões suspeitos.
4.2. Identificamos operações que possam indicar lavagem de dinheiro.
4.3. Analisamos transações incomuns ou acima de limites estabelecidos.

5. COMUNICAÇÃO DE OPERAÇÕES SUSPEITAS

5.1. Comunicamos imediatamente ao COAF (Conselho de Controle de Atividades Financeiras) operações suspeitas.
5.2. Mantemos sigilo sobre as comunicações realizadas.
5.3. Não informamos ao cliente sobre a comunicação de operação suspeita.

6. LIMITES E CONTROLES

6.1. Estabelecemos limites de transação conforme perfil do usuário.
6.2. Implementamos controles de risco baseados em análise de perfil.
6.3. Realizamos revisões periódicas de limites e controles.

7. TREINAMENTO E CAPACITAÇÃO

7.1. Nossa equipe recebe treinamento regular sobre PLD/CFT.
7.2. Mantemos programa de capacitação contínua.
7.3. Atualizamos procedimentos conforme mudanças na legislação.

8. REGISTROS E DOCUMENTAÇÃO

8.1. Mantemos registros detalhados de todas as operações.
8.2. Documentamos análises de risco e decisões tomadas.
8.3. Conservamos documentos pelo prazo legal estabelecido.

9. COOPERAÇÃO COM AUTORIDADES

9.1. Cooperamos com autoridades competentes em investigações.
9.2. Fornecemos informações quando legalmente exigido.
9.3. Respeitamos ordens judiciais e determinações regulatórias.

10. PROIBIÇÕES

10.1. Não processamos transações de origem ou destino desconhecido.
10.2. Não aceitamos operações que violem sanções internacionais.
10.3. Bloqueamos contas envolvidas em atividades suspeitas.

11. REVISÃO E ATUALIZAÇÃO

11.1. Esta política é revisada periodicamente.
11.2. Atualizamos procedimentos conforme mudanças na legislação.
11.3. Comunicamos alterações relevantes aos usuários.

12. CONTATO

Para questões relacionadas a PLD/CFT:
- E-mail: compliance@seudominio.com
- Através do suporte da Plataforma

Última atualização: ' . date('d/m/Y'),
                'is_active' => true,
            ],
            [
                'slug' => 'manual-kyc',
                'title' => 'Manual KYC - Conheça seu Cliente',
                'content' => 'MANUAL KYC - CONHEÇA SEU CLIENTE

1. O QUE É KYC?

KYC (Know Your Customer - Conheça seu Cliente) é um processo de identificação e verificação de identidade que garante a segurança e conformidade da plataforma PagueMax.

2. POR QUE É NECESSÁRIO?

2.1. Cumprimento legal: Exigido por lei para prevenção à lavagem de dinheiro.
2.2. Segurança: Protege você e outros usuários contra fraudes.
2.3. Conformidade: Garante que a plataforma opere dentro da legalidade.

3. QUANDO FAZER O KYC?

3.1. Ao criar sua conta na plataforma.
3.2. Antes de realizar sua primeira transação.
3.3. Quando solicitado pela plataforma para atualização de dados.

4. DOCUMENTOS NECESSÁRIOS

4.1. Para Pessoa Física:
- Documento de identidade com foto (RG, CNH ou RNE)
- CPF
- Comprovante de endereço (conta de luz, água, telefone ou extrato bancário)
- Selfie segurando o documento de identidade

4.2. Para Pessoa Jurídica:
- Contrato Social ou Estatuto Social
- CNPJ
- Documento de identidade do representante legal
- Comprovante de endereço da empresa
- Selfie do representante legal segurando documento

5. COMO ENVIAR OS DOCUMENTOS

5.1. Acesse a seção KYC no seu dashboard.
5.2. Preencha seus dados de endereço completo.
3. Faça upload dos documentos solicitados.
4. Envie uma selfie segurando seu documento de identidade.
5. Aguarde a análise da equipe.

6. REQUISITOS DAS FOTOS

6.1. Documentos:
- Fotos nítidas e legíveis
- Todas as informações visíveis
- Sem cortes ou partes faltando
- Boa iluminação

6.2. Selfie:
- Rosto claramente visível
- Documento de identidade visível na mão
- Boa iluminação
- Sem óculos escuros ou objetos cobrindo o rosto

7. PRAZO DE ANÁLISE

7.1. Análise geralmente concluída em até 48 horas úteis.
7.2. Em caso de necessidade de informações adicionais, você será notificado.
7.3. O prazo pode variar conforme volume de solicitações.

8. STATUS DO KYC

8.1. Pendente: Documentos enviados, aguardando análise.
8.2. Aprovado: KYC concluído com sucesso, conta liberada.
8.3. Rejeitado: Documentos não atendem aos requisitos (você será notificado do motivo).

9. O QUE FAZER SE FOR REJEITADO?

9.1. Verifique o motivo da rejeição na notificação.
9.2. Corrija os problemas identificados.
9.3. Reenvie os documentos corrigidos.
9.4. Entre em contato com o suporte se tiver dúvidas.

10. SEGURANÇA DOS DADOS

10.1. Seus documentos são armazenados com segurança.
10.2. Utilizamos criptografia para proteção dos dados.
10.3. Apenas equipe autorizada tem acesso aos documentos.
10.4. Dados são mantidos conforme exigências legais.

11. ATUALIZAÇÃO DE DADOS

11.1. Mantenha seus dados sempre atualizados.
11.2. Informe alterações de endereço ou documentos.
11.3. A plataforma pode solicitar atualização periódica.

12. DÚVIDAS FREQUENTES

12.1. Posso usar a plataforma sem KYC?
Não, o KYC é obrigatório para uso completo da plataforma.

12.2. Meus dados estão seguros?
Sim, seguimos rigorosos padrões de segurança e privacidade.

12.3. Quanto tempo leva a aprovação?
Geralmente até 48 horas úteis.

13. CONTATO

Para dúvidas sobre o processo KYC:
- Através do suporte da plataforma
- E-mail: suporte@seudominio.com

Última atualização: ' . date('d/m/Y'),
                'is_active' => true,
            ],
        ];

        foreach ($pages as $pageData) {
            StaticPage::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }
}
