# Guida Rapida ai Comandi Essenziali di Docker e Docker Compose

Una raccolta pratica dei comandi Docker più usati per la gestione quotidiana di container, immagini, volumi, reti e ambienti multi-container.

---

## 1. Gestione dei Container

I container sono le istanze in esecuzione delle tue immagini.

| Comando | Descrizione |
| :--- | :--- |
| `docker run -d --name <nome> -p <host_port>:<container_port> <immagine>` | Avvia un container in background (detached) specificando nome e porte. |
| `docker ps` | Elenca tutti i container attualmente in esecuzione. |
| `docker ps -a` | Elenca **tutti** i container (in esecuzione e fermati). |
| `docker stop <container>` | Arresta in modo graduale uno o più container. |
| `docker start <container>` | Avvia un container precedentemente fermato. |
| `docker restart <container>` | Riavvia un container in esecuzione. |
| `docker exec -it <container> bash` | Apre una shell interattiva Bash all'interno del container (usa `sh` se `bash` non è disponibile). |
| `docker logs -f --tail 100 <container>` | Mostra gli ultimi 100 log e rimane in ascolto in tempo reale (`-f`). |
| `docker rm <container>` | Elimina un container fermato. |
| `docker rm -f <container>` | Forza l'eliminazione di un container anche se è in esecuzione. |

---

## 2. Gestione delle Immagini

Le immagini sono i template di sola lettura usati per creare i container.

| Comando | Descrizione |
| :--- | :--- |
| `docker images` | Elenca tutte le immagini scaricate o create localmente. |
| `docker pull <immagine>:<tag>` | Scarica un'immagine da Docker Hub (es. `docker pull nginx:latest`). |
| `docker build -t <nome_immagine>:<tag> .` | Costruisce un'immagine a partire dal `Dockerfile` presente nella cartella corrente. |
| `docker rmi <immagine>` | Rimuove un'immagine locale (se non è usata da container). |
| `docker image prune` | Rimuove tutte le immagini "dangling" (non etichettate e inutilizzate). |

---

## 3. Gestione dei Volumi

I volumi permettono di rendere persistenti i dati generati o modificati dai container.

| Comando | Descrizione |
| :--- | :--- |
| `docker volume ls` | Elenca tutti i volumi presenti nel sistema. |
| `docker volume create <nome_volume>` | Crea un nuovo volume gestito da Docker. |
| `docker volume inspect <nome_volume>` | Mostra i dettagli di un volume (incluso il percorso su disco locale). |
| `docker volume rm <nome_volume>` | Elimina uno specifico volume (deve essere scollegato da container). |
| `docker volume prune` | Elimina tutti i volumi non utilizzati da alcun container. |

> **Trucco per Docker Compose:**  
> Per eliminare solo un volume specifico e mantenere gli altri:
> 1. `docker compose down` (ferma i container senza toccare i dati)
> 2. `docker volume rm <nome_progetto>_<nome_volume>` (rimuove solo il volume scelto)
> 3. `docker compose up -d` (ricrea il volume mancante)

---

## 4. Gestione delle Reti (Network)

Le reti consentono la comunicazione tra container distinti.

| Comando | Descrizione |
| :--- | :--- |
| `docker network ls` | Elenca tutte le reti create. |
| `docker network create <nome_rete>` | Crea una nuova rete di tipo bridge. |
| `docker network connect <rete> <container>` | Connette un container esistente a una rete. |
| `docker network disconnect <rete> <container>` | Disconnette un container da una rete. |
| `docker network rm <nome_rete>` | Rimuove una rete custom. |

---

## 5. Docker Compose

Strumento per definire ed eseguire applicazioni multi-container tramite file `docker-compose.yml`.

| Comando | Descrizione |
| :--- | :--- |
| `docker compose up -d` | Avvia tutti i servizi definiti nel file in background. |
| `docker compose down` | Ferma e rimuove container e reti creati da Compose. |
| `docker compose down -v` | Ferma i servizi ed **elimina tutti i volumi** associati. |
| `docker compose ps` | Mostra lo stato dei container gestiti dal progetto locale. |
| `docker compose logs -f <servizio>` | Visualizza i log in tempo reale di uno specifico servizio. |
| `docker compose exec <servizio> <comando>` | Esegue un comando all'interno del container del servizio (es. `exec db bash`). |
| `docker compose restart <servizio>` | Riavvia solo uno specifico servizio del progetto. |

---

## 6. Manutenzione e Diagnostica di Sistema

Comandi essenziali per monitorare le risorse e ripulire il sistema.

| Comando | Descrizione |
| :--- | :--- |
| `docker stats` | Mostra l'utilizzo in tempo reale di CPU, RAM e Rete per ogni container. |
| `docker top <container>` | Visualizza i processi attivi all'interno di un container. |
| `docker system df` | Mostra lo spazio su disco occupato da container, immagini e volumi. |
| `docker system prune` | Rimuove container fermati, reti non usate e immagini inutilizzate. |
| `docker system prune -a --volumes` | **Pulizia profonda:** Elimina tutto ciò che non è in uso (immagini, container, volumi). |
