<?php

$en = require dirname(__DIR__).'/en/mma.php';

$en['authorization']['protected'] = 'Protegido';
$en['roles']['names'] = ['super_manager' => 'Root', 'admin' => 'Administrador', 'publisher' => 'Publicador', 'sales' => 'Vendas', 'checkin' => 'Controle de entrada', 'support' => 'Suporte', 'subscriber' => 'Assinante'];
$en['menu'] = [
    'dashboard' => 'Início',
    'events' => ['group' => 'Eventos', 'events' => 'Eventos', 'fights' => 'Lutas', 'results' => 'Resultados', 'media' => 'Mídia de eventos'],
    'fighters' => ['group' => 'Lutadores', 'fighters' => 'Lutadores', 'teams' => 'Equipes e academias', 'weight_classes' => 'Categorias de peso', 'rankings' => 'Rankings'],
    'content' => ['group' => 'Conteúdo', 'news' => 'Notícias', 'landing' => 'Landing page', 'sponsors' => 'Patrocinadores'],
    'commerce' => ['group' => 'Assinaturas e pagamentos', 'plans' => 'Planos', 'subscribers' => 'Assinantes', 'subscriptions' => 'Assinaturas', 'payments' => 'Pagamentos', 'purchase_requests' => 'Solicitações de compra'],
    'tickets' => ['group' => 'Ingressos', 'links' => 'Links de ingressos', 'orders' => 'Pedidos de ingressos', 'checkins' => 'Validação de entrada'],
    'security' => ['group' => 'Usuários e segurança', 'users' => 'Usuários', 'roles' => 'Perfis e permissões'],
    'settings' => ['group' => 'Configuração', 'system' => 'Ajustes do sistema', 'notifications' => 'Notificações', 'logs' => 'Logs'],
];
$en['admin']['common'] = ['active' => 'Ativo', 'inactive' => 'Inativo', 'empty' => 'Não há registros para mostrar.', 'not_available' => 'Não disponível', 'yes' => 'Sim', 'no' => 'Não', 'developed_by' => 'Desenvolvido por', 'filters' => ['search' => 'Buscar', 'status' => 'Status', 'all' => 'Todos', 'per_page' => 'Por página', 'clear' => 'Limpar'], 'columns' => ['actions' => 'Ações']];
$en['admin']['events'] = [
    'page_title' => 'Eventos',
    'table_title' => 'Lista de eventos',
    'table_subtitle' => 'Filtre, crie, edite, publique ou exclua eventos sem dependências ativas.',
    'create' => 'Novo evento',
    'edit' => 'Editar evento',
    'delete_title' => 'Excluir evento',
    'delete_warning' => 'Esta ação excluirá o evento:',
    'search_placeholder' => 'Nome, subtítulo ou slug...',
    'content_summary' => ':fights lutas · :tickets links',
    'image_help' => 'Imagens JPG, PNG ou WebP são otimizadas ao salvar. Tamanho máximo: 5 MB.',
    'filters' => ['venue' => 'Sede', 'featured' => 'Destaque', 'from' => 'De', 'to' => 'Até'],
    'columns' => ['event' => 'Evento', 'venue' => 'Sede', 'date' => 'Data', 'content' => 'Conteúdo', 'status' => 'Status', 'featured' => 'Destaque'],
    'status' => ['draft' => 'Rascunho', 'published' => 'Publicado', 'archived' => 'Arquivado', 'cancelled' => 'Cancelado'],
    'actions' => ['publish' => 'Publicar'],
    'form' => ['name' => 'Nome', 'slug' => 'Slug', 'subtitle' => 'Subtítulo', 'description' => 'Descrição', 'venue_id' => 'Sede', 'starts_at' => 'Data e hora do evento', 'doors_open_at' => 'Abertura dos portões', 'timezone' => 'Fuso horário', 'stream_url' => 'URL da transmissão', 'ticket_url' => 'URL de ingressos', 'status' => 'Status', 'is_featured' => 'Marcar como destaque', 'poster_image' => 'Pôster', 'banner_image' => 'Banner'],
    'messages' => ['created' => 'Evento registrado com sucesso.', 'updated' => 'Evento atualizado com sucesso.', 'published' => 'Evento publicado com sucesso.', 'deleted' => 'Evento excluído com sucesso.', 'delete_blocked' => 'Não é possível excluir um evento com lutas, links de ingressos ou solicitações associadas.'],
];
$en['admin']['event_media'] = [
    'page_title' => 'Mídia de eventos',
    'table_title' => 'Lista de mídia',
    'table_subtitle' => 'Filtre, envie, edite ou exclua peças públicas de mídia dos eventos.',
    'create' => 'Nova peça',
    'edit' => 'Editar peça',
    'delete_title' => 'Excluir mídia',
    'delete_warning' => 'Esta ação excluirá a peça de mídia:',
    'search_placeholder' => 'Título, descrição ou evento...',
    'image_help' => 'Imagens JPG, PNG ou WebP são otimizadas ao salvar. Tamanho máximo: 5 MB.',
    'featured' => 'Destaque',
    'untitled' => 'Sem título',
    'filters' => ['event' => 'Evento', 'file_type' => 'Tipo', 'category' => 'Categoria'],
    'columns' => ['media' => 'Mídia', 'event' => 'Evento', 'category' => 'Categoria', 'order' => 'Ordem', 'status' => 'Status'],
    'file_types' => ['image' => 'Imagem', 'video' => 'Vídeo'],
    'categories' => ['gallery' => 'Galeria', 'weigh_in' => 'Pesagem', 'faceoff' => 'Encarada', 'backstage' => 'Backstage', 'highlight' => 'Highlight', 'sponsor' => 'Patrocinador', 'other' => 'Outro'],
    'form' => ['event_id' => 'Evento', 'file_type' => 'Tipo de arquivo', 'file_path' => 'URL do vídeo', 'category' => 'Categoria', 'title' => 'Título', 'description' => 'Descrição', 'is_featured' => 'Marcar como destaque', 'display_order' => 'Ordem', 'status' => 'Status', 'media_image' => 'Imagem'],
    'messages' => ['created' => 'Peça de mídia registrada com sucesso.', 'updated' => 'Peça de mídia atualizada com sucesso.', 'deleted' => 'Peça de mídia excluída com sucesso.'],
    'validation' => ['image_required' => 'Você deve selecionar uma imagem para salvar esta peça de mídia.'],
];

$en['admin']['news'] = [
    'page_title' => 'Notícias',
    'table_title' => 'Lista de notícias',
    'table_subtitle' => 'Filtre, crie, edite, publique ou exclua publicações editoriais.',
    'create' => 'Nova notícia',
    'edit' => 'Editar notícia',
    'delete_title' => 'Excluir notícia',
    'delete_warning' => 'Esta ação excluirá a notícia:',
    'search_placeholder' => 'Título, slug ou resumo...',
    'image_help' => 'A imagem de capa JPG, PNG ou WebP é otimizada ao salvar. Tamanho máximo: 5 MB.',
    'filters' => ['featured' => 'Destaque', 'from' => 'Desde', 'to' => 'Até'],
    'columns' => ['post' => 'Notícia', 'author' => 'Autor', 'published_at' => 'Publicação', 'status' => 'Status', 'featured' => 'Destaque'],
    'status' => ['draft' => 'Rascunho', 'published' => 'Publicado', 'archived' => 'Arquivado'],
    'actions' => ['publish' => 'Publicar'],
    'form' => ['title' => 'Título', 'slug' => 'Slug', 'excerpt' => 'Resumo', 'content' => 'Conteúdo', 'status' => 'Status', 'is_featured' => 'Marcar como destaque', 'published_at' => 'Data de publicação', 'cover_image' => 'Imagem de capa'],
    'messages' => ['created' => 'Notícia registrada com sucesso.', 'updated' => 'Notícia atualizada com sucesso.', 'published' => 'Notícia publicada com sucesso.', 'deleted' => 'Notícia excluída com sucesso.'],
];

$en['admin']['fights'] = [
    'page_title' => 'Lutas',
    'table_title' => 'Lista de lutas',
    'table_subtitle' => 'Filtre, programe, edite ou exclua lutas sem resultado oficial.',
    'create' => 'Nova luta',
    'edit' => 'Editar luta',
    'delete_title' => 'Excluir luta',
    'delete_warning' => 'Esta ação excluirá a luta:',
    'search_placeholder' => 'Título, lutador ou apelido...',
    'image_help' => 'Imagens promocionais JPG, PNG ou WebP são otimizadas ao salvar. Tamanho máximo: 5 MB.',
    'filters' => ['event' => 'Evento', 'bout_type' => 'Tipo', 'weight_class' => 'Categoria'],
    'columns' => ['fight' => 'Luta', 'event' => 'Evento', 'weight_class' => 'Categoria', 'rounds' => 'Rounds', 'order' => 'Ordem', 'status' => 'Status'],
    'status' => ['scheduled' => 'Programada', 'live' => 'Ao vivo', 'finished' => 'Finalizada', 'cancelled' => 'Cancelada'],
    'bout_type' => ['regular' => 'Regular', 'main_event' => 'Luta principal', 'co_main_event' => 'Co-principal', 'title_fight' => 'Disputa de título', 'exhibition' => 'Exibição'],
    'flags' => ['main_event' => 'Principal', 'featured' => 'Destaque', 'has_result' => 'Com resultado'],
    'form' => ['event_id' => 'Evento', 'weight_class_id' => 'Categoria de peso', 'corner_red_fighter_id' => 'Canto vermelho', 'corner_blue_fighter_id' => 'Canto azul', 'title' => 'Título', 'bout_type' => 'Tipo de luta', 'rounds' => 'Rounds', 'display_order' => 'Ordem no card', 'starts_at' => 'Data e hora da luta', 'status' => 'Status', 'is_main_event' => 'Luta principal', 'is_featured' => 'Destaque', 'notes' => 'Notas internas', 'promo_image' => 'Imagem promocional'],
    'messages' => ['created' => 'Luta registrada com sucesso.', 'updated' => 'Luta atualizada com sucesso.', 'deleted' => 'Luta excluída com sucesso.', 'delete_blocked' => 'Não é possível excluir uma luta com resultado oficial.'],
];
$en['admin']['fight_results'] = [
    'page_title' => 'Resultados',
    'table_title' => 'Resultados das lutas',
    'table_subtitle' => 'Filtre lutas, revise resultados oficiais e registre vencedor, método, round e tempo.',
    'search_placeholder' => 'Evento, luta, lutador ou método...',
    'modal_title' => 'Gerenciar resultado oficial',
    'pending' => 'Pendente',
    'no_custom_title' => 'Sem título personalizado',
    'round_time_value' => 'R: :round · T: :time',
    'filters' => ['event' => 'Evento', 'result_type' => 'Resultado', 'result_state' => 'Registro'],
    'result_state' => ['with' => 'Com resultado', 'without' => 'Sem resultado'],
    'columns' => ['fight' => 'Luta', 'event' => 'Evento', 'result' => 'Resultado', 'winner' => 'Vencedor', 'round_time' => 'Round / tempo'],
    'result_types' => ['ko_tko' => 'KO/TKO', 'submission' => 'Finalização', 'decision' => 'Decisão', 'draw' => 'Empate', 'no_contest' => 'Sem resultado', 'disqualification' => 'Desclassificação'],
    'corners' => ['red' => 'Vermelho: :fighter', 'blue' => 'Azul: :fighter'],
    'actions' => ['manage' => 'Gerenciar resultado'],
    'form' => ['result_type' => 'Tipo de resultado', 'winner_fighter_id' => 'Vencedor', 'no_winner' => 'Sem vencedor', 'method' => 'Método', 'round' => 'Round', 'time' => 'Tempo', 'official_notes' => 'Notas oficiais'],
    'messages' => ['saved' => 'Resultado oficial salvo com sucesso.'],
    'validation' => ['winner_required' => 'É necessário selecionar um vencedor para este tipo de resultado.', 'winner_corner' => 'O vencedor deve pertencer ao corner vermelho ou azul da luta.', 'round_limit' => 'O round não pode ser maior que os :rounds rounds configurados para a luta.'],
];

$en['admin']['fighters'] = [
    'page_title' => 'Lutadores',
    'table_title' => 'Lista de lutadores',
    'table_subtitle' => 'Filtre, crie, edite ou exclua lutadores sem lutas nem rankings associados.',
    'create' => 'Novo lutador',
    'edit' => 'Editar lutador',
    'delete_title' => 'Excluir lutador',
    'delete_warning' => 'Esta ação excluirá o lutador:',
    'search_placeholder' => 'Nome, apelido ou slug...',
    'record_summary' => ':wins-:losses-:draws · NC :nc',
    'image_help' => 'Imagens JPG, PNG ou WebP são otimizadas ao salvar. Tamanho máximo: 5 MB.',
    'filters' => ['gender' => 'Gênero', 'weight_class' => 'Categoria', 'team' => 'Equipe'],
    'columns' => ['fighter' => 'Lutador', 'team' => 'Equipe', 'weight_class' => 'Categoria', 'record' => 'Recorde', 'fights' => 'Lutas', 'status' => 'Status'],
    'gender' => ['male' => 'Masculino', 'female' => 'Feminino'],
    'stance' => ['orthodox' => 'Ortodoxa', 'southpaw' => 'Canhota', 'switch' => 'Alternada'],
    'form' => ['first_name' => 'Nome', 'last_name' => 'Sobrenome', 'nickname' => 'Apelido', 'slug' => 'Slug', 'gender' => 'Gênero', 'country_id' => 'País', 'city_id' => 'Cidade', 'fighter_team_id' => 'Equipe/academia', 'weight_class_id' => 'Categoria de peso', 'birthdate' => 'Data de nascimento', 'height_cm' => 'Altura (cm)', 'reach_cm' => 'Alcance (cm)', 'stance' => 'Base', 'bio' => 'Biografia', 'wins' => 'Vitórias', 'losses' => 'Derrotas', 'draws' => 'Empates', 'no_contests' => 'Sem resultado', 'status' => 'Status', 'profile_image' => 'Imagem de perfil', 'cover_image' => 'Imagem de capa'],
    'messages' => ['created' => 'Lutador registrado com sucesso.', 'updated' => 'Lutador atualizado com sucesso.', 'deleted' => 'Lutador excluído com sucesso.', 'delete_blocked' => 'Não é possível excluir um lutador com lutas ou rankings associados.'],
];
$en['admin']['sponsors'] = ['page_title' => 'Patrocinadores', 'table_title' => 'Lista de patrocinadores', 'table_subtitle' => 'Filtre, crie, edite ou exclua patrocinadores sem eventos vinculados.', 'create' => 'Novo patrocinador', 'edit' => 'Editar patrocinador', 'delete_title' => 'Excluir patrocinador', 'delete_warning' => 'Esta ação excluirá o patrocinador:', 'search_placeholder' => 'Nome, slug, site ou e-mail...', 'image_help' => 'Logos JPG, PNG ou WebP são otimizados ao salvar. Tamanho máximo: 5 MB.', 'events_summary' => ':count eventos', 'events_help' => 'Mantenha Ctrl ou Cmd pressionado para selecionar vários eventos.', 'filters' => ['event' => 'Evento'], 'columns' => ['sponsor' => 'Patrocinador', 'website' => 'Site', 'email' => 'E-mail', 'events' => 'Eventos', 'order' => 'Ordem', 'status' => 'Status'], 'form' => ['name' => 'Nome', 'slug' => 'Slug', 'website_url' => 'Site', 'contact_email' => 'E-mail de contato', 'description' => 'Descrição', 'display_order' => 'Ordem', 'status' => 'Status', 'logo_path' => 'Logo', 'events' => 'Eventos vinculados'], 'messages' => ['created' => 'Patrocinador registrado com sucesso.', 'updated' => 'Patrocinador atualizado com sucesso.', 'deleted' => 'Patrocinador excluído com sucesso.', 'delete_blocked' => 'Não é possível excluir um patrocinador vinculado a eventos.']];
$en['admin']['subscription_plans'] = ['page_title' => 'Planos de assinatura', 'table_title' => 'Lista de planos', 'table_subtitle' => 'Filtre, crie, edite ou exclua planos sem uso ativo.', 'create' => 'Novo plano', 'edit' => 'Editar plano', 'delete_title' => 'Excluir plano', 'delete_warning' => 'Esta ação excluirá o plano:', 'search_placeholder' => 'Nome, slug ou descrição...', 'duration_summary' => ':days dias', 'discount_summary' => ':discount% de desconto', 'features_summary' => ':count benefícios', 'usage_summary' => ':subscriptions assinaturas - :requests solicitações', 'filters' => ['billing_period' => 'Período'], 'columns' => ['plan' => 'Plano', 'price' => 'Preço', 'period' => 'Período', 'usage' => 'Uso', 'order' => 'Ordem', 'status' => 'Status'], 'billing_periods' => ['monthly' => 'Mensal', 'quarterly' => 'Trimestral', 'yearly' => 'Anual', 'one_time' => 'Pagamento único', 'lifetime' => 'Vitalício'], 'form' => ['name' => 'Nome', 'slug' => 'Slug', 'description' => 'Descrição', 'price' => 'Preço', 'currency' => 'Moeda', 'billing_period' => 'Período de cobrança', 'duration_days' => 'Duração em dias', 'discount_percentage' => 'Percentual de desconto', 'display_order' => 'Ordem', 'status' => 'Status'], 'features' => ['title' => 'Benefícios', 'add' => 'Adicionar benefício', 'name' => 'Benefício', 'description' => 'Descrição', 'feature_key' => 'Chave técnica', 'value' => 'Valor', 'display_order' => 'Ordem', 'status' => 'Status', 'help' => 'Somente benefícios com nome são salvos. Benefícios ativos também são sincronizados no campo JSON features do plano.'], 'messages' => ['created' => 'Plano registrado com sucesso.', 'updated' => 'Plano atualizado com sucesso.', 'deleted' => 'Plano excluído com sucesso.', 'delete_blocked' => 'Não é possível excluir um plano com assinaturas ou solicitações de compra associadas.']];
$en['admin']['subscribers'] = ['page_title' => 'Assinantes', 'table_title' => 'Lista de assinantes', 'table_subtitle' => 'Filtre, revise e atualize dados básicos do assinante sem alterar seus perfis.', 'edit' => 'Editar assinante', 'search_placeholder' => 'Nome, e-mail, telefone ou documento...', 'identity_value' => 'Documento: :value', 'last_login_value' => 'Último acesso: :date', 'last_login_empty' => 'Nenhum acesso registrado', 'activity_summary' => ':subscriptions assinaturas - :payments pagamentos - :requests solicitações', 'filters' => ['subscription_status' => 'Assinatura'], 'columns' => ['subscriber' => 'Assinante', 'contact' => 'Contato', 'subscription' => 'Assinatura', 'activity' => 'Atividade', 'status' => 'Status'], 'subscription_status' => ['none' => 'Sem assinatura', 'pending' => 'Pendente', 'active' => 'Ativa', 'expired' => 'Expirada', 'cancelled' => 'Cancelada', 'suspended' => 'Suspensa'], 'form' => ['name' => 'Nome', 'lastname' => 'Sobrenome', 'email' => 'E-mail', 'number_phone' => 'Telefone', 'identity_document' => 'Documento de identidade', 'state' => 'Status da conta'], 'messages' => ['updated' => 'Assinante atualizado com sucesso.']];

$en['admin']['rankings'] = [
    'page_title' => 'Rankings',
    'table_title' => 'Lista de rankings',
    'table_subtitle' => 'Filtre, crie e ajuste posições oficiais por categoria e gênero.',
    'create' => 'Novo ranking',
    'edit' => 'Editar ranking',
    'search_placeholder' => 'Lutador, apelido ou categoria...',
    'champion' => 'Campeão',
    'filters' => ['weight_class' => 'Categoria', 'gender' => 'Gênero'],
    'columns' => ['position' => 'Posição', 'fighter' => 'Lutador', 'weight_class' => 'Categoria', 'record' => 'Cartel', 'movement' => 'Movimento', 'status' => 'Status'],
    'movement' => ['same' => 'Sem mudança', 'up' => 'Sobe :places', 'down' => 'Desce :places'],
    'form' => ['weight_class_id' => 'Categoria de peso', 'gender' => 'Gênero', 'fighter_id' => 'Lutador', 'position' => 'Posição atual', 'previous_position' => 'Posição anterior', 'is_champion' => 'Marcar como campeão', 'ranked_at' => 'Data do ranking', 'status' => 'Status'],
    'messages' => ['created' => 'Ranking registrado com sucesso.', 'updated' => 'Ranking atualizado com sucesso.'],
    'validation' => ['gender_mismatch' => 'O lutador selecionado não pertence ao gênero do ranking.', 'weight_class_mismatch' => 'O lutador selecionado não pertence à categoria de peso do ranking.', 'position_taken' => 'Já existe um ranking ativo com essa posição para a categoria e o gênero selecionados.'],
];

$en['admin']['fighter_teams'] = [
    'page_title' => 'Equipes e academias',
    'table_title' => 'Lista de equipes',
    'table_subtitle' => 'Filtre, crie, edite ou exclua equipes sem lutadores associados.',
    'create' => 'Nova equipe',
    'edit' => 'Editar equipe',
    'delete_title' => 'Excluir equipe',
    'delete_warning' => 'Esta ação excluirá a equipe:',
    'search_placeholder' => 'Nome, coach, telefone ou slug...',
    'image_help' => 'Logos JPG, PNG ou WebP são otimizados ao salvar. Tamanho máximo: 5 MB.',
    'filters' => ['city' => 'Cidade'],
    'columns' => ['team' => 'Equipe', 'city' => 'Cidade', 'coach' => 'Coach', 'contact' => 'Contato', 'fighters' => 'Lutadores', 'status' => 'Status'],
    'form' => ['name' => 'Nome', 'slug' => 'Slug', 'city_id' => 'Cidade', 'coach_name' => 'Coach', 'contact_phone' => 'Telefone', 'description' => 'Descrição', 'status' => 'Status', 'logo_path' => 'Logo'],
    'messages' => ['created' => 'Equipe registrada com sucesso.', 'updated' => 'Equipe atualizada com sucesso.', 'deleted' => 'Equipe excluída com sucesso.', 'delete_blocked' => 'Não é possível excluir uma equipe com lutadores associados.'],
];
$en['admin']['purchase_requests'] = [
    'page_title' => 'Solicitações de compra',
    'table_title' => 'Lista de solicitações',
    'table_subtitle' => 'Filtre, atribua responsáveis e atualize o status de cada solicitação.',
    'search_placeholder' => 'Nome, e-mail, telefone ou UUID...',
    'modal_title' => 'Gerenciar solicitação',
    'delete_title' => 'Excluir solicitação',
    'delete_warning' => 'Esta ação excluirá a solicitação de:',
    'filters' => ['request_type' => 'Tipo', 'channel' => 'Canal', 'assigned_to' => 'Atribuído', 'from' => 'De', 'to' => 'Até'],
    'columns' => ['request' => 'Solicitação', 'contact' => 'Contato', 'related_to' => 'Relacionado a', 'status' => 'Status', 'assigned_to' => 'Atribuído', 'created_at' => 'Data'],
    'status' => ['pending' => 'Pendente', 'in_review' => 'Em revisão', 'contacted' => 'Contactado', 'converted' => 'Convertido', 'closed' => 'Fechado', 'rejected' => 'Rejeitado'],
    'request_types' => ['general_contact' => 'Contato geral', 'event_ticket' => 'Ingressos para evento', 'subscription' => 'Assinatura', 'payment_proof' => 'Comprovante de pagamento'],
    'channels' => ['whatsapp' => 'WhatsApp', 'phone' => 'Telefone', 'email' => 'E-mail'],
    'assignment' => ['unassigned' => 'Sem atribuição', 'me' => 'Atribuídas a mim'],
    'actions' => ['manage' => 'Gerenciar', 'assign_to_me' => 'Atribuir a mim', 'close' => 'Fechar'],
    'details' => ['title' => 'Detalhes da solicitação', 'contact' => 'Contato', 'request_type' => 'Tipo', 'email' => 'E-mail', 'channel' => 'Canal', 'phone' => 'Telefone/WhatsApp', 'related_to' => 'Relacionado a', 'message' => 'Mensagem', 'proof' => 'Comprovante'],
    'proof' => ['available' => 'Comprovante', 'open' => 'Abrir comprovante privado', 'none' => 'Nenhum comprovante carregado'],
    'form' => ['status' => 'Status', 'assigned_to' => 'Responsável', 'notes' => 'Notas internas'],
    'messages' => ['updated' => 'Solicitação atualizada com sucesso.', 'assigned' => 'Solicitação atribuída com sucesso.', 'closed' => 'Solicitação fechada com sucesso.', 'deleted' => 'Solicitação excluída com sucesso.'],
];
$en['admin']['dashboard']['page_title'] = 'Painel principal';
$en['admin']['weight_classes']['page_title'] = 'Categorias de peso';
$en['landing'] = ['login' => 'Entrar', 'hero_text' => 'Eventos, lutas, rankings e assinaturas para uma promotora de artes marciais.', 'view_events' => 'Ver eventos', 'contact' => 'Contato', 'events_title' => 'Eventos ativos', 'events_subtitle' => 'Eventos publicados, próximos e anteriores que continuam ativos.', 'featured' => 'Destaque', 'empty_events' => 'Ainda não há eventos publicados.', 'no_image' => 'Sem imagem disponível', 'back' => 'Voltar', 'fights_title' => 'Card de lutas', 'empty_fights' => 'Este evento ainda não tem lutas publicadas.', 'vs' => 'vs', 'nav' => ['home' => 'Início', 'fighters' => 'Lutadores', 'news' => 'Notícias', 'subscription' => 'Assinatura', 'contact' => 'Contato'], 'footer' => ['quick_links' => 'Links rápidos', 'contact' => 'Contato', 'follow_us' => 'Siga-nos', 'rights' => 'Todos os direitos reservados.'], 'fighters' => ['featured_title' => 'Lutadores em destaque', 'featured_subtitle' => 'Conheça alguns dos atletas do nosso elenco.', 'view_all' => 'Ver todos os lutadores', 'title' => 'Lutadores', 'subtitle' => 'Conheça o elenco completo de lutadores ativos.', 'empty' => 'Ainda não há lutadores publicados.', 'wins' => 'Vitórias', 'losses' => 'Derrotas', 'draws' => 'Empates', 'bio_title' => 'Biografia', 'fight_history' => 'Histórico de lutas', 'round' => 'Round', 'result_win' => 'Vitória', 'result_loss' => 'Derrota', 'result_pending' => 'Pendente', 'no_fights' => 'Ainda não há lutas registradas.'], 'news' => ['section_title' => 'Últimas notícias', 'view_all' => 'Ver todas as notícias', 'title' => 'Notícias', 'subtitle' => 'Comunicados, novidades e entrevistas.', 'read_more' => 'Leia mais', 'empty' => 'Ainda não há notícias publicadas.'], 'event' => ['prev' => 'Evento anterior', 'next' => 'Próximo evento', 'main_event' => 'Luta principal', 'tickets_title' => 'Ingressos', 'price_from' => 'A partir de', 'no_tickets' => 'Os links de venda serão publicados em breve.', 'contact_cta' => 'Fale conosco'], 'subscription' => ['title' => 'Assinatura', 'subtitle' => 'Escolha o plano ideal para você e aproveite benefícios exclusivos.', 'empty' => 'Não há planos disponíveis no momento.', 'cta' => 'Quero assinar'], 'contact_page' => ['title' => 'Contato', 'subtitle' => 'Envie sua solicitação e nossa equipe entrará em contato em breve.', 'about_event' => 'Você está entrando em contato sobre: :event', 'about_plan' => 'Você está entrando em contato sobre: :plan', 'success' => 'Sua solicitação foi enviada com sucesso. Entraremos em contato em breve.', 'direct_title' => 'Prefere falar diretamente conosco?', 'direct_hint' => 'Você também pode entrar em contato com nossa equipe por qualquer um destes canais.', 'form' => ['name' => 'Nome completo', 'email' => 'E-mail', 'phone' => 'Telefone', 'whatsapp' => 'WhatsApp', 'channel' => 'Canal de contato preferido', 'type' => 'Motivo do contato', 'message' => 'Mensagem', 'proof' => 'Comprovante de pagamento', 'proof_hint' => 'Opcional. Formatos aceitos: JPG, JPEG, PNG ou PDF, no máximo 5 MB.', 'submit' => 'Enviar solicitação'], 'channel_options' => ['whatsapp' => 'WhatsApp', 'phone' => 'Telefone', 'email' => 'E-mail'], 'type_options' => ['general_contact' => 'Consulta geral', 'event_ticket' => 'Ingressos de evento', 'subscription' => 'Assinatura', 'payment_proof' => 'Comprovante de pagamento']]];
$en['uploads']['payment_proofs'] = ['invalid_type' => 'Formato não suportado. Use JPG, JPEG, PNG ou PDF.', 'max_size' => 'O comprovante não pode ter mais de :max MB.'];
$en['uploads']['public_images'] = ['invalid_type' => 'Formato não suportado. Use JPG, PNG, GIF ou WebP.', 'max_size' => 'A imagem não pode ter mais de :max MB.', 'process_failed' => 'Não foi possível processar a imagem selecionada.'];

$en['admin']['user_subscriptions'] = [
    'page_title' => 'Assinaturas de usuário',
    'table_title' => 'Lista de assinaturas',
    'table_subtitle' => 'Filtre, crie, edite ou cancele assinaturas de usuários sem processar pagamentos automáticos.',
    'create' => 'Nova assinatura',
    'edit' => 'Editar assinatura',
    'cancel_title' => 'Cancelar assinatura',
    'cancel_warning' => 'Esta ação marcará a assinatura como cancelada:',
    'search_placeholder' => 'Assinante, e-mail, telefone, plano ou slug...',
    'period_value' => ':start - :end',
    'open_ended' => 'Sem data final',
    'renewal_value' => 'Renovação: :date',
    'payments_summary' => ':count pagamentos',
    'filters' => [
        'plan' => 'Plano',
        'from' => 'De',
        'to' => 'Até',
    ],
    'columns' => [
        'subscriber' => 'Assinante',
        'plan' => 'Plano',
        'period' => 'Período',
        'status' => 'Status',
        'payments' => 'Pagamentos',
        'source' => 'Origem',
    ],
    'status' => [
        'pending' => 'Pendente',
        'active' => 'Ativa',
        'expired' => 'Expirada',
        'cancelled' => 'Cancelada',
        'suspended' => 'Suspensa',
    ],
    'sources' => [
        'manual' => 'Manual',
        'admin' => 'Administração',
        'purchase_request' => 'Solicitação de compra',
        'import' => 'Importação',
        'other' => 'Outro',
    ],
    'actions' => [
        'cancel' => 'Cancelar assinatura',
    ],
    'form' => [
        'user_id' => 'Assinante',
        'subscription_plan_id' => 'Plano',
        'starts_at' => 'Data e hora de início',
        'ends_at' => 'Data e hora de término',
        'trial_ends_at' => 'Fim do período de teste',
        'renewal_at' => 'Data de renovação',
        'status' => 'Status',
        'source' => 'Origem',
        'metadata_note' => 'Nota interna',
    ],
    'messages' => [
        'created' => 'Assinatura registrada com sucesso.',
        'updated' => 'Assinatura atualizada com sucesso.',
        'cancelled' => 'Assinatura cancelada com sucesso.',
    ],
    'validation' => [
        'subscriber_required' => 'O usuário selecionado deve ter o perfil de assinante.',
        'ends_at_after_start' => 'A data final deve ser posterior ou igual à data de início.',
        'trial_ends_at_after_start' => 'A data de teste deve ser posterior ou igual à data de início.',
        'renewal_at_after_start' => 'A data de renovação deve ser posterior ou igual à data de início.',
    ],
];

$en['admin']['subscription_payments'] = [
    'page_title' => 'Pagamentos de assinatura',
    'table_title' => 'Lista de pagamentos',
    'table_subtitle' => 'Filtre, registre, atualize, confirme ou cancele pagamentos manuais.',
    'create' => 'Novo pagamento',
    'edit' => 'Editar pagamento',
    'confirm_title' => 'Confirmar pagamento',
    'confirm_warning' => 'Esta ação marcará o pagamento como pago:',
    'cancel_title' => 'Cancelar pagamento',
    'cancel_warning' => 'Esta ação marcará o pagamento como falho:',
    'search_placeholder' => 'Assinante, e-mail, telefone, plano, provedor ou transação...',
    'paid_at_value' => 'Pago: :date',
    'not_paid' => 'Sem data de pagamento',
    'filters' => [
        'payment_method' => 'Método',
        'from' => 'De',
        'to' => 'Até',
    ],
    'columns' => [
        'subscriber' => 'Assinante',
        'subscription' => 'Assinatura',
        'amount' => 'Valor',
        'method' => 'Método',
        'proof' => 'Comprovante',
        'status' => 'Status',
    ],
    'status' => [
        'pending' => 'Pendente',
        'paid' => 'Pago',
        'failed' => 'Falho',
        'refunded' => 'Reembolsado',
        'expired' => 'Expirado',
    ],
    'payment_methods' => [
        'manual_transfer' => 'Transferência manual',
        'cash' => 'Dinheiro',
        'qr' => 'Pagamento QR',
        'whatsapp' => 'WhatsApp',
        'gateway' => 'Gateway',
        'other' => 'Outro',
    ],
    'actions' => [
        'confirm' => 'Confirmar pagamento',
        'cancel' => 'Cancelar pagamento',
    ],
    'proof' => [
        'open' => 'Abrir comprovante',
        'none' => 'Sem comprovante',
        'help' => 'JPG, JPEG, PNG ou PDF. Imagens são otimizadas ao salvar. Tamanho máximo: 5 MB.',
    ],
    'form' => [
        'user_id' => 'Assinante',
        'user_subscription_id' => 'Assinatura',
        'no_subscription' => 'Sem assinatura vinculada',
        'amount' => 'Valor',
        'currency' => 'Moeda',
        'payment_method' => 'Método de pagamento',
        'provider' => 'Provedor',
        'provider_transaction_id' => 'ID da transação',
        'payment_url' => 'URL de pagamento',
        'paid_at' => 'Data de pagamento',
        'expires_at' => 'Data de vencimento',
        'status' => 'Status',
        'notes' => 'Notas internas',
        'payment_proof' => 'Comprovante de pagamento',
    ],
    'messages' => [
        'created' => 'Pagamento registrado com sucesso.',
        'updated' => 'Pagamento atualizado com sucesso.',
        'confirmed' => 'Pagamento confirmado com sucesso.',
        'cancelled' => 'Pagamento cancelado com sucesso.',
    ],
    'validation' => [
        'subscriber_required' => 'O usuário selecionado deve ter o perfil de assinante.',
        'subscription_user_mismatch' => 'A assinatura selecionada não pertence ao assinante selecionado.',
    ],
];

$en['admin']['ticket_links'] = [
    'page_title' => 'Links de ingressos',
    'table_title' => 'Lista de links de ingressos',
    'table_subtitle' => 'Filtre, crie, edite ou exclua links públicos de venda associados a eventos.',
    'create' => 'Novo link',
    'edit' => 'Editar link',
    'delete_title' => 'Excluir link de ingressos',
    'delete_warning' => 'Esta ação excluirá o link de ingressos:',
    'search_placeholder' => 'Provedor, etiqueta, URL ou evento...',
    'open_start' => 'Sem limite de início',
    'open_end' => 'Sem limite final',
    'filters' => [
        'event' => 'Evento',
        'sale_channel' => 'Canal',
    ],
    'columns' => [
        'link' => 'Link',
        'event' => 'Evento',
        'channel' => 'Canal',
        'price' => 'Preço desde',
        'window' => 'Janela de venda',
        'status' => 'Status',
    ],
    'sale_channels' => [
        'external' => 'Plataforma externa',
        'whatsapp' => 'WhatsApp',
        'phone' => 'Telefone',
        'streaming' => 'Streaming',
        'vip' => 'VIP',
        'other' => 'Outro',
    ],
    'form' => [
        'event_id' => 'Evento',
        'provider_name' => 'Provedor',
        'label' => 'Etiqueta visível',
        'sale_channel' => 'Canal de venda',
        'url' => 'URL',
        'price_from' => 'Preço desde',
        'currency' => 'Moeda',
        'starts_at' => 'Início da venda',
        'ends_at' => 'Fim da venda',
        'display_order' => 'Ordem',
        'status' => 'Status',
    ],
    'messages' => [
        'created' => 'Link de ingressos registrado com sucesso.',
        'updated' => 'Link de ingressos atualizado com sucesso.',
        'deleted' => 'Link de ingressos excluído com sucesso.',
    ],
    'validation' => [
        'ends_after_start' => 'A data final da venda deve ser posterior ou igual à data de início.',
    ],
];

$en['admin']['system_settings'] = [
    'page_title' => 'Configurações do sistema',
    'form_title' => 'Configuração geral',
    'form_subtitle' => 'Esses valores são refletidos no painel administrativo, no login e na landing pública.',
    'image_help' => 'Imagens JPG, PNG ou WebP são otimizadas ao salvar. Tamanho máximo: 5 MB.',
    'sections' => [
        'identity' => 'Identidade',
        'contact' => 'Contato',
        'social' => 'Redes sociais',
        'seo' => 'Landing pública e SEO',
    ],
    'form' => [
        'product_name' => 'Nome do produto',
        'public_title' => 'Título público',
        'contact_email' => 'E-mail de contato',
        'contact_phone' => 'Telefone de contato',
        'whatsapp_phone' => 'Telefone do WhatsApp',
        'short_description' => 'Descrição curta',
        'seo_title' => 'Título SEO',
        'seo_description' => 'Descrição SEO',
        'landing_show_rankings' => 'Mostrar rankings na landing pública',
        'logo_path' => 'Logo',
        'favicon_path' => 'Favicon',
    ],
    'social' => [
        'facebook' => 'URL do Facebook',
        'instagram' => 'URL do Instagram',
        'youtube' => 'URL do YouTube',
        'tiktok' => 'URL do TikTok',
    ],
    'actions' => [
        'save' => 'Salvar configurações',
    ],
    'messages' => [
        'updated' => 'Configurações do sistema atualizadas com sucesso.',
    ],
];

$en['menu']['events']['venues'] = 'Sedes';
$en['admin']['venues'] = [
    'page_title' => 'Sedes',
    'table_title' => 'Lista de sedes',
    'table_subtitle' => 'Filtre, crie, edite ou exclua sedes sem eventos relacionados.',
    'create' => 'Nova sede',
    'edit' => 'Editar sede',
    'delete_title' => 'Excluir sede',
    'delete_warning' => 'Esta ação excluirá a sede:',
    'search_placeholder' => 'Nome, endereço, contato, telefone ou slug...',
    'image_help' => 'Imagens JPG, PNG ou WebP são otimizadas ao salvar. Tamanho máximo: 5 MB.',
    'filters' => ['city' => 'Cidade'],
    'columns' => ['venue' => 'Sede', 'location' => 'Localização', 'capacity' => 'Capacidade', 'contact' => 'Contato', 'events' => 'Eventos', 'status' => 'Status'],
    'form' => ['name' => 'Nome', 'slug' => 'Slug', 'city_id' => 'Cidade', 'address' => 'Endereço', 'latitude' => 'Latitude', 'longitude' => 'Longitude', 'capacity' => 'Capacidade', 'contact_name' => 'Nome de contato', 'contact_phone' => 'Telefone de contato', 'status' => 'Status', 'image' => 'Imagem da sede'],
    'messages' => ['created' => 'Sede registrada com sucesso.', 'updated' => 'Sede atualizada com sucesso.', 'deleted' => 'Sede excluída com sucesso.', 'delete_blocked' => 'Uma sede com eventos relacionados não pode ser excluída.'],
];

$en['admin']['users'] = [
    'page_title' => 'Usuários',
    'table_title' => 'Lista de usuários',
    'table_subtitle' => 'Filtre, crie, edite ou exclua usuários permitidos pela hierarquia de papéis configurada.',
    'create' => 'Novo usuário',
    'edit' => 'Editar usuário',
    'delete_title' => 'Excluir usuário',
    'delete_warning' => 'Esta ação excluirá o usuário:',
    'search_placeholder' => 'Nome, e-mail, telefone ou documento...',
    'password_help' => 'Ao editar, deixe este campo vazio para manter a senha atual.',
    'selected_roles' => 'papéis selecionados',
    'hierarchy_readonly' => 'Somente leitura por hierarquia',
    'filters' => ['role' => 'Papel'],
    'columns' => ['user' => 'Usuário', 'contact' => 'Contato', 'roles' => 'Papéis', 'last_login' => 'Último acesso', 'status' => 'Status'],
    'form' => ['name' => 'Nome', 'lastname' => 'Sobrenome', 'email' => 'E-mail', 'number_phone' => 'Telefone', 'identity_document' => 'Documento de identidade', 'state' => 'Status', 'password' => 'Senha', 'roles' => 'Papéis'],
    'messages' => ['created' => 'Usuário registrado com sucesso.', 'updated' => 'Usuário atualizado com sucesso.', 'deleted' => 'Usuário excluído com sucesso.', 'self_delete_blocked' => 'Você não pode excluir sua própria conta de usuário.'],
    'validation' => ['roles_allowed' => 'Selecione pelo menos um papel que você tenha permissão para atribuir.'],
];

$en['menu']['fighters']['media'] = 'Mídia de lutadores';
$en['admin']['fighter_media'] = [
    'page_title' => 'Mídia de lutadores',
    'table_title' => 'Lista de mídia de lutadores',
    'table_subtitle' => 'Filtre, envie, edite ou exclua mídias públicas de lutadores.',
    'create' => 'Nova mídia',
    'edit' => 'Editar mídia',
    'delete_title' => 'Excluir mídia',
    'delete_warning' => 'Esta ação excluirá a mídia:',
    'search_placeholder' => 'Título, descrição, lutador ou apelido...',
    'image_help' => 'Imagens JPG, PNG ou WebP são otimizadas ao salvar. Tamanho máximo: 5 MB.',
    'featured' => 'Destacado',
    'untitled' => 'Sem título',
    'filters' => ['fighter' => 'Lutador', 'file_type' => 'Tipo'],
    'columns' => ['media' => 'Mídia', 'fighter' => 'Lutador', 'order' => 'Ordem', 'status' => 'Status'],
    'file_types' => ['image' => 'Imagem', 'video' => 'Vídeo'],
    'form' => ['fighter_id' => 'Lutador', 'file_type' => 'Tipo de arquivo', 'file_path' => 'URL do vídeo', 'title' => 'Título', 'description' => 'Descrição', 'is_featured' => 'Marcar como destaque', 'display_order' => 'Ordem', 'status' => 'Status', 'media_image' => 'Imagem'],
    'messages' => ['created' => 'Mídia de lutador registrada com sucesso.', 'updated' => 'Mídia de lutador atualizada com sucesso.', 'deleted' => 'Mídia de lutador excluída com sucesso.'],
    'validation' => ['image_required' => 'Você deve selecionar uma imagem para salvar esta mídia de lutador.'],
];

$en['admin']['landing'] = [
    'page_title' => 'Landing pública',
    'form_title' => 'Configuração da landing',
    'form_subtitle' => 'Estes valores aparecem na página pública e devem permanecer alinhados aos eventos publicados ativos.',
    'latest_events' => 'Pré-visualização de eventos publicados',
    'empty_events' => 'Não há eventos publicados para pré-visualizar.',
    'stats' => ['published' => 'Publicados', 'featured' => 'Destacados', 'drafts' => 'Rascunhos'],
    'actions' => ['save' => 'Salvar landing', 'open_public' => 'Abrir página pública', 'manage_events' => 'Gerenciar eventos'],
    'form' => ['public_title' => 'Título público', 'short_description' => 'Descrição curta', 'seo_title' => 'Título SEO', 'seo_description' => 'Descrição SEO', 'landing_show_rankings' => 'Mostrar rankings na landing pública'],
    'messages' => ['updated' => 'Landing pública atualizada com sucesso.'],
];

$en['subscriber_portal'] = [
    'menu' => ['dashboard' => 'Início', 'purchases' => 'Minhas compras', 'events' => 'Meus eventos', 'subscription' => 'Minha assinatura', 'profile' => 'Perfil'],
    'dashboard' => ['title' => 'Início do assinante', 'subtitle' => 'Revise sua conta, assinatura, compras e eventos disponíveis.', 'cards' => ['account' => 'Status da conta', 'subscription' => 'Status da assinatura', 'events' => 'Eventos disponíveis'], 'latest_purchases' => 'Últimos pagamentos', 'latest_requests' => 'Últimas solicitações', 'next_events' => 'Eventos disponíveis'],
    'actions' => ['view_all' => 'Ver tudo', 'save' => 'Salvar alterações', 'back_to_purchases' => 'Voltar para minhas compras'],
    'empty' => ['no_subscription' => 'Sem assinatura ativa', 'no_purchases' => 'Nenhuma compra registrada.', 'no_requests' => 'Nenhuma solicitação registrada.', 'no_events' => 'Você não tem eventos disponíveis.'],
    'events' => ['title' => 'Meus eventos', 'subtitle' => 'Eventos associados às suas solicitações, compras ou benefícios de acesso.'],
    'purchases' => ['title' => 'Minhas compras', 'subtitle' => 'Revise pagamentos e solicitações registrados na sua conta.', 'payments' => 'Pagamentos', 'requests' => 'Solicitações', 'detail_title' => 'Detalhe da compra', 'detail_subtitle' => 'Informações completas deste pagamento ou solicitação.', 'proof_status' => 'Status do comprovante', 'proof_uploaded_label' => 'Comprovante enviado', 'proof_missing_label' => 'Sem comprovante enviado', 'notes' => 'Notas', 'message' => 'Mensagem', 'related_event' => 'Evento associado', 'upload_proof_title' => 'Enviar ou reenviar comprovante', 'upload_proof_hint' => 'Formatos aceitos: JPG, JPEG, PNG ou PDF, no máximo 5 MB.', 'upload_proof_submit' => 'Enviar comprovante', 'proof_field' => 'Comprovante de pagamento', 'proof_uploaded' => 'Comprovante enviado com sucesso. Nossa equipe irá revisá-lo em breve.', 'proof_not_allowed' => 'Este registro não aceita mais um novo comprovante. Entre em contato com o suporte.', 'contact_title' => 'Precisa de ajuda com esta compra?', 'contact_hint' => 'Entre em contato com nossa equipe de vendas ou suporte por qualquer um destes canais.'],
    'subscription' => ['title' => 'Minha assinatura', 'subtitle' => 'Revise seu plano atual e o histórico de assinaturas.', 'current' => 'Assinatura atual', 'history' => 'Histórico de assinaturas', 'benefits' => 'Benefícios do plano', 'contact_title' => 'Quer renovar ou fazer upgrade?', 'contact_hint' => 'Entre em contato com nossa equipe de vendas para renovar, fazer upgrade ou tirar dúvidas sobre sua assinatura.'],
    'profile' => ['title' => 'Perfil', 'subtitle' => 'Atualize seus próprios dados de contato.', 'updated' => 'Perfil atualizado com sucesso.', 'form' => ['name' => 'Nome', 'lastname' => 'Sobrenome', 'email' => 'E-mail', 'number_phone' => 'Telefone', 'identity_document' => 'Documento de identidade', 'current_password' => 'Senha atual', 'new_password' => 'Nova senha', 'confirm_password' => 'Confirmar senha'], 'password_title' => 'Alterar senha', 'password_hint' => 'Use pelo menos 8 caracteres e evite reutilizar senhas antigas.', 'password_updated' => 'Senha atualizada com sucesso.', 'password_submit' => 'Atualizar senha'],
    'columns' => ['concept' => 'Conceito', 'amount' => 'Valor', 'method' => 'Método', 'status' => 'Status', 'date' => 'Data', 'channel' => 'Canal', 'plan' => 'Plano', 'start' => 'Início', 'end' => 'Fim', 'period' => 'Período'],
];

$en['menu']['reports'] = ['group' => 'Relatórios', 'events' => 'Relatórios de eventos', 'subscriptions' => 'Relatórios de assinaturas', 'sales' => 'Relatórios de vendas'];
$en['admin']['reports'] = [
    'page_title' => 'Relatórios',
    'table_title' => 'Relatórios operacionais',
    'table_subtitle' => 'Filtre por tipo de relatório, intervalo de datas e status sem modificar registros.',
    'types' => ['events' => 'Eventos', 'subscriptions' => 'Assinaturas', 'sales' => 'Vendas'],
    'filters' => ['type' => 'Tipo de relatório', 'from' => 'Desde', 'to' => 'Até'],
    'stats' => ['total' => 'Total', 'published' => 'Publicados', 'featured' => 'Destacados', 'requests' => 'Solicitações', 'active' => 'Ativas', 'pending' => 'Pendentes', 'expired' => 'Expiradas', 'paid' => 'Pagos', 'amount' => 'Valor pago'],
    'columns' => ['event' => 'Evento', 'venue' => 'Local', 'date' => 'Data', 'fights' => 'Lutas', 'requests' => 'Solicitações', 'subscriber' => 'Assinante', 'plan' => 'Plano', 'period' => 'Período', 'payments' => 'Pagamentos', 'concept' => 'Conceito', 'amount' => 'Valor', 'method' => 'Método'],
];

$en['admin']['logs'] = [
    'page_title' => 'Logs',
    'table_title' => 'Log da aplicação',
    'file_size' => 'Tamanho do arquivo',
    'last_modified' => 'Última modificação',
    'total_entries' => 'Entradas totais',
    'filtered_entries' => 'Entradas filtradas',
    'detail_title' => 'Detalhe da entrada do log',
    'context' => 'Contexto',
    'trace' => 'Rastreamento',
    'raw' => 'Entrada original',
    'has_context' => 'Inclui contexto',
    'has_trace' => 'Inclui rastreamento',
    'filters' => ['level' => 'Nível', 'all_levels' => 'Todos os níveis', 'from_date' => 'Desde', 'to_date' => 'Até', 'search_placeholder' => 'Buscar mensagem, contexto ou rastreamento...'],
    'columns' => ['datetime' => 'Data e hora', 'level' => 'Nível', 'env' => 'Ambiente', 'message' => 'Mensagem'],
    'actions' => ['download' => 'Baixar log', 'view_detail' => 'Ver detalhe'],
];

$en['landing']['fighters']['result_draw'] = 'Empate / Sem resultado';

return $en;
