# POC: Server-Sent Events (SSE) com PHP e HTML

Esta é uma Prova de Conceito (POC) para demonstrar o funcionamento do **Server-Sent Events (SSE)** utilizando PHP no backend e HTML/JavaScript no frontend, rodando em um ambiente Dockerizado.

## O que é o SSE?
O Server-Sent Events (SSE) é um padrão HTML5 que permite que o navegador receba atualizações automáticas de um servidor via uma conexão HTTP. É uma comunicação **unidirecional** (Server -> Client), ideal para feeds de notícias, cotações de ações, placares ao vivo ou notificações, onde o cliente não precisa enviar dados continuamente, apenas recebê-los.

## ⚠️ O que saber sobre o SSE (Pontos de Atenção)
Para que o SSE funcione corretamente em produção, fique atento a estes pontos:

1. **Output Buffering:** O PHP e servidores web (como Apache/Nginx) costumam fazer buffer da resposta antes de enviá-la ao cliente. Para o SSE funcionar, é obrigatório desativar ou "descarregar" (flush) esse buffer em cada iteração, garantindo que o dado chegue em tempo real.
2. **Timeouts de Conexão:** Por ser uma conexão longa e persistente, servidores web e balanceadores de carga podem encerrar conexões ociosas (ex: `proxy_read_timeout` no Nginx).
3. **Limite de Conexões:** No HTTP/1.1, os navegadores limitam o número de conexões simultâneas ao mesmo domínio (geralmente 6). Se você abrir 6 abas desta POC, a 7ª ficará travada esperando uma aba ser fechada. O uso de HTTP/2 resolve esse problema através de multiplexação.
4. **Headers Específicos:** O servidor deve sempre responder com o cabeçalho `Content-Type: text/event-stream`.

### Como esta arquitetura resolve os problemas do SSE?

1. **Limite de 6 conexões (HTTP/1.1):** Navegadores limitam conexões simultâneas a um mesmo domínio no HTTP/1.1. Ao gerar um certificado SSL autoassinado e habilitar o **HTTP/2**, utilizamos a **multiplexação**. Você pode abrir dezenas de abas no navegador sem que as conexões fiquem travadas na fila.
2. **Output Buffering:** O FastCGI do Nginx nativamente segura a resposta do PHP até que o buffer encha. Resolvemos isso de duas formas:
   - No `nginx.conf`, usando `fastcgi_buffering off;`.
   - No PHP, enviando o header `X-Accel-Buffering: no` e desligando o buffer no `php.ini`.
3. **Timeouts:** Conexões SSE são longas. O Nginx derrubaria a conexão por inatividade. O parâmetro `fastcgi_read_timeout 3600;` mantém a conexão viva por até uma hora.

## Requisitos
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## Como executar

1. Clone o repositório.
2. Na raiz do projeto, suba os containers com o comando:
   ```bash
   docker-compose up -d --build

1. Acesse no navegador: https://localhost:8443

    Aviso: Como estamos usando um certificado SSL autoassinado para habilitar o HTTP/2 localmente, o navegador exibirá um alerta de segurança ("Sua conexão não é particular"). Clique em Avançado e depois em Ir para localhost (inseguro).

2. Abra a aba "Network" (Rede) no Developer Tools (F12) do seu navegador. Você verá a requisição para server.php marcando o protocolo como h2 (HTTP/2) e recebendo os pacotes de dados continuamente sem fechar a conexão.