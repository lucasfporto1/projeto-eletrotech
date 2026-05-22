# ⚡ EletroTech - Desafio Técnico

O **EletroTech** é um sistema web desenvolvido como validação técnica de estágio, simulando o back-office de uma empresa de soluções elétricas. O foco do projeto é a aplicação prática da arquitetura MVC, integridade de dados relacionais e segurança da informação utilizando PHP puro.

## 🎯 Funcionalidades e Regras de Negócio
* **Gestão de Pessoal:** Controle de eletricistas via *Soft Delete* para preservação de histórico. A listagem utiliza agregações SQL dinâmicas para calcular em tempo real a quantidade de OS realizadas e a meta do mês vigente.
* **Controle de Inventário:** Gestão de materiais com trava de segurança no banco de dados contra saldo negativo e perda de histórico.
* **Ordens de Serviço (OS):** Simulação de operações de campo. O sistema debita o estoque automaticamente através de **Transações ACID** (`begin_transaction`, `commit`, `rollback`). As OS registradas tornam-se imutáveis.
* **Gestão de Metas:** Atribuição de objetivos de desempenho financeiro imutáveis, com filtros de busca dinâmicos por período e funcionário.
* **Assistente de IA:** Chatbot integrado à interface que consome a API do Flowise (via cURL) para suporte e navegação.

## 🛠️ Tecnologias e Arquitetura Base
O sistema foi construído sem frameworks externos no back-end:

* **Back-end:** PHP 8+ (Padrão MVC).
* **Banco de Dados:** MySQL (Uso estrito de *Prepared Statements* para evitar injeções SQL).
* **Front-end:** HTML5, CSS3, JavaScript (ES6) e Bootstrap 5.3.

## 📦 Bibliotecas Autorais (Core do Sistema)
O projeto implementa bibliotecas e módulos nativos para a infraestrutura do sistema:
* **`lib-php` (Core Back-end):** Mini-framework customizado que gerencia validações, *Rate Limiter* (proteção contra ataques de força bruta no login), *Hashing* de senhas, checagem de sessões e carregamento seguro de variáveis de ambiente (`.env`).
* **`ml-ui` e `js-lib`:** Componentes de interface padronizados e funções de validação em JavaScript (como formatação e verificação de regras de CPF).

## 🚀 Como Executar Localmente

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/lucasfporto1/projeto-eletrotech.git
   ```

2. **Configuração do Ambiente (`.env`):**
   Crie um arquivo chamado `.env` na raiz do projeto contendo as suas credenciais de banco e API:
   ```env
   DB_HOST=localhost
   DB_USER=seu_usuario
   DB_PASS=sua_senha
   DB_NAME=nome_do_banco
   FLOWISE_API_URL=sua_url_da_api_flowise
   ```

3. **Banco de Dados (Importação do SQL):**
   No seu servidor MySQL, crie um banco de dados e importe o script SQL fornecido na raiz do repositório (`banco_eletrotech.sql`) para gerar as tabelas e os vínculos relacionais.

4. **Inicie o Servidor:**
   Coloque a pasta do projeto no diretório público do seu servidor web local (ex: `htdocs` no XAMPP e MAMP).

5. **Acesso:**
   Abra o navegador e acesse a tela de login:
   `http://localhost/caminho-para-sua-pasta/view/telas/loginView.php`
