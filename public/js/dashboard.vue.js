+(function () {
    if (!document.getElementById('dashboardPanel')) {
        return;
    }
    Vue.use(VueTheMask);

    window.vueDashboard = new Vue({
        el: "#dashboardPanel",
        apiBase: 'api/',

        data: {
            copyButton: null,
            componentes: {
                diarios: [
                    {
                        size: '3',
                        title: 'Vidas Ativas',
                        endpoint: 'vidasAtivas',
                        type: 'number',
                        icon: 'fa fa-paw',
                        color: 'bg-green bg-font-green',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Vendas',
                        endpoint: 'vendas',
                        type: 'number',
                        icon: 'fa fa-dollar',
                        color: 'bg-purple bg-font-purple hidden-badges',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Cancelamentos',
                        endpoint: 'cancelamentos',
                        type: 'number',
                        icon: 'fa fa-ban',
                        color: 'bg-blue bg-font-blue',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'NPS',
                        endpoint: 'nps',
                        type: 'number',
                        icon: 'fa fa-line-chart',
                        color: 'bg-green-jungle bg-font-green',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                ],
                absolutos: [
                    {
                        size: '3',
                        title: 'Vendas',
                        endpoint: 'vendas',
                        type: 'number',
                        icon: 'fa fa-dollar',
                        color: 'bg-purple-wisteria bg-font-purple-wisteria',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Cancelamentos',
                        endpoint: 'cancelamentos',
                        type: 'number',
                        icon: 'fa fa-ban',
                        color: 'bg-blue-sharp bg-font-blue-sharp',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Sinistralidade',
                        endpoint: 'sinistralidadeMensal',
                        type: 'money',
                        icon: 'fa fa-pie-chart',
                        color: 'bg-red-pink bg-font-red-pink',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Inadimplência',
                        endpoint: 'atrasoMensal',
                        type: 'money',
                        icon: 'fa fa-hourglass',
                        color: 'bg-purple-soft bg-font-purple-soft',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Faturamento',
                        endpoint: 'faturamentoMensal',
                        type: 'money',
                        icon: 'fa fa-money',
                        color: 'bg-green-dark bg-font-green-dark',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    // {
                    //     size: '3',
                    //     title: 'MRM',
                    //     endpoint: 'mediaRecorrenteMensal',
                    //     type: 'money',
                    //     icon: 'fa fa-money',
                    //     color: 'bg-green-haze bg-font-green-haze',
                    //     data: null,
                    //     permission: false,
                    //     loading: true,
                    // },
                    // {
                    //     size: '3',
                    //     title: 'Previsto Mês',
                    //     endpoint: 'faturamentoMensalPrevisto',
                    //     type: 'money',
                    //     icon: 'fa fa-money',
                    //     color: 'bg-green-steel bg-font-green-steel',
                    //     data: null,
                    //     permission: false,
                    //     loading: true,
                    // },
                    {
                        size: '3',
                        title: 'Participação Mensal',
                        endpoint: 'participativos',
                        type: 'money',
                        icon: 'fa fa-star-half-o',
                        color: 'bg-blue bg-font-blue',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Renovações',
                        endpoint: 'statusPetsPlanos/R',
                        type: 'number',
                        icon: 'fa fa-retweet',
                        color: 'bg-yellow-gold-opacity bg-font-yellow-gold',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Upgrades',
                        endpoint: 'statusPetsPlanos/U',
                        type: 'number',
                        icon: 'fa fa-level-up',
                        color: 'bg-green-jungle-opacity bg-font-green-jungle',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '3',
                        title: 'Downgrades',
                        endpoint: 'statusPetsPlanos/D',
                        type: 'number',
                        icon: 'fa fa-level-down',
                        color: 'bg-red-flamingo-opacity bg-font-red-flamingo',
                        data: null,
                        permission: false,
                        loading: true,
                    },
                ],
                temporais: [
                    {
                        size: '6',
                        divId: 'novas-vidas-serial',
                        title: 'Vidas Ativas',
                        endpoint: 'novasVidasSerial',
                        type: 'line',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '6',
                        divId: 'cancelamentos-serial',
                        title: 'Cancelamentos x Novas Vidas',
                        endpoint: 'cancelamentosSerial',
                        type: 'serial',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '12',
                        divId: 'pets-por-plano',
                        title: 'Pets x Plano',
                        endpoint: 'petsPorPlano',
                        type: 'serial',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '12',
                        divId: 'pets-por-idade',
                        title: 'Pets x Idade',
                        endpoint: 'petsPorIdade',
                        type: 'serial',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '12',
                        divId: 'ranking-vendedores',
                        title: 'Ranking dos Vendedores (TOP 10)',
                        endpoint: 'rankingVendedores',
                        type: 'serial',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '12',
                        divId: 'comissao-vendas',
                        title: 'Comissão de Vendas',
                        endpoint: 'comissaoVendas',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '12',
                        divId: 'sinistralidade-por-credenciada',
                        title: 'Sinistalidade x Credenciada',
                        endpoint: 'sinistralidadePorCredenciada',
                        type: 'pie',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '6',
                        divId: 'caes',
                        title: 'Cães',
                        endpoint: 'caes',
                        type: 'pie',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '6',
                        divId: 'gatos',
                        title: 'Gatos',
                        endpoint: 'gatos',
                        type: 'pie',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'castrados-nao-castrados',
                        title: 'Castrados x Não castrados',
                        endpoint: 'castradosVersusNaoCastrados',
                        type: 'pie',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'participativos-versus-integrais',
                        title: 'Participativos x Integrais',
                        endpoint: 'petsParticipativosVersusIntegrais',
                        type: 'pie',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '6',
                        divId: 'vencimento-de-vacinas',
                        title: 'Vencimentos de Vacinas',
                        endpoint: 'vencimentoVacinas',
                        type: 'pie',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '6',
                        divId: 'controle-de-vacinas',
                        title: 'Controle de Vacinas',
                        endpoint: 'controleVacinas',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '6',
                        divId: 'pets-aniversariantes',
                        title: 'Pets Aniversariantes',
                        endpoint: 'petsAniversariantes',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'clientes-aniversariantes',
                        title: 'Clientes Aniversariantes',
                        endpoint: 'clientesAniversariantes',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'pets-por-bairro',
                        title: 'Pets x Bairro',
                        endpoint: 'petsPorBairro',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'pets-por-cidade',
                        title: 'Pets x Cidade',
                        endpoint: 'petsPorCidade',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'pets-inativos-por-bairro',
                        title: 'Pets x Bairro (inativos)',
                        endpoint: 'petsInativosPorBairro',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'pets-inativos-por-cidade',
                        title: 'Pets x Cidade (inativos)',
                        endpoint: 'petsInativosPorCidade',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'sinistralidade-por-prestador',
                        title: 'Sinistralidade por Prestador',
                        endpoint: 'sinistralidadePorPrestador',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true,
                    },
                    {
                        size: '6',
                        divId: 'vendas-por-vendedor',
                        title: 'Vendas x Vendedor',
                        endpoint: 'vendasPorVendedor',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '6',
                        divId: 'rentabilidade-de-plano',
                        title: 'Rentabilidade de Plano',
                        endpoint: 'rentabilidadeDePlano',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true
                    },
                    {
                        size: '12',
                        divId: 'ranking-procedimentos',
                        title: 'Ranking de Procedimentos x Credenciados',
                        endpoint: 'rankingProcedimentos',
                        type: 'table',
                        data: [],
                        permission: false,
                        loading: true
                    },
                ]
            },
            filtros: {
                diario: {
                    dataInputStart: null,
                    dataInputEnd: null,
                    visivel: false,
                    dataEscolhida: null,
                    dataAplicada: null
                },
                absoluto: {
                    dataInputStart: null,
                    dataInputEnd: null,
                    visivel: false,
                    dataEscolhida: null,
                    dataAplicada: null
                },
                temporal: {
                    dataInputStart: null,
                    dataInputEnd: null,
                    visivel: false,
                    dataEscolhida: null,
                    dataAplicada: null
                }
            },
            isSaving: false
        },
        methods: {
            setDataEscolhida: function(filtro, data) {
                if(data === 'input') {
                    if(this.filtros[filtro].dataInputStart.length === 'XX/XX/XXXX'.length) {
                        this.filtros[filtro].dataEscolhida = this.filtros[filtro].dataInputStart;
                    }
                    return;
                }

                this.filtros[filtro].dataEscolhida = data;
            },
            aplicarData: function(filtro) {
                if(!this.filtros[filtro].dataEscolhida) {
                    //TODO: aplicar validaçao de data.
                    return false;
                }

                this.filtros[filtro].dataAplicada = this.filtros[filtro].dataEscolhida;
                this.filtros[filtro].visivel = false;

                if(filtro === 'temporal') {
                    this.loadTemporais();
                } else if(filtro === 'absolutos') {
                    this.loadAbsolutos();
                } else {
                    this.loadDiarios();
                }

            },
            cancelarData: function(filtro) {
                if(this.filtros[filtro].dataEscolhida == this.filtros[filtro].dataAplicada == this.filtros[filtro].dataInputStart == null) {
                    this.filtros[filtro].visivel = false;
                }
                this.filtros[filtro].dataEscolhida = this.filtros[filtro].dataAplicada = this.filtros[filtro].dataInputStart;
                this.filtros[filtro].visivel = false;
            },
            mostrarFiltro: function(filtro) {
                this.filtros[filtro].visivel = !this.filtros[filtro].visivel;
            },
            loadDiarios: function() {
                var componentesDiarios = this.componentes.diarios;
                var _self = this;
                componentesDiarios.forEach(function(c,i) {
                    var index = i;
                    var params = {};
                    if(_self.filtros.diario.dataInputStart) {
                        params.start = _self.filtros.diario.dataInputStart;
                        if(_self.filtros.diario.dataInputEnd) {
                            params.end = _self.filtros.diario.dataInputEnd;
                        } else {
                            params.end = _self.filtros.diario.dataAplicada;
                        }
                    } else {
                        params.start = moment().format('DD/MM/YYYY');
                        params.end = moment().format('DD/MM/YYYY');
                    }

                    if (params.start === moment().format('DD/MM/YYYY')) {
                        params.start = moment().subtract(1, 'day').format('DD/MM/YYYY');
                    }

                    if (params.end === moment().format('DD/MM/YYYY')) {
                        params.end = moment().subtract(1, 'day').format('DD/MM/YYYY');
                    }

                    _self.componentes.diarios[index].loading = true;
                    _self.$http.get('dashboard/api/' + c.endpoint, {
                        params: params,
                    }).then(function (response) {
                        _self.componentes.diarios[index].data = response.body;
                        _self.componentes.diarios[index].permission = response.body.permission;
                        _self.componentes.diarios[index].loading = false;
                        _self.$nextTick(function () {
                            _self.tooltip();
                        });
                    }, function (response) {
                        console.error("Error trying to retrieve data on 'dashboard/api/'" + c.endpoint);
                    });
                });
            },
            loadAbsolutos: function() {
                var componentesAbsolutos = this.componentes.absolutos;
                var _self = this;
                componentesAbsolutos.forEach(function(c,i) {
                    var index = i;
                    var params = {};
                    if(_self.filtros.absoluto.dataInputStart) {
                        params.start = _self.filtros.absoluto.dataInputStart;
                        if(_self.filtros.absoluto.dataInputEnd) {
                            params.end = _self.filtros.absoluto.dataInputEnd;
                        } else {
                            params.end = _self.filtros.absoluto.dataAplicada;
                        }
                    } else {
                        params.end = _self.filtros.absoluto.dataAplicada;
                    }

                    _self.componentes.absolutos[index].loading = true;
                    _self.$http.get('dashboard/api/' + c.endpoint, {
                        params: params,
                    }).then(function (response) {
                        _self.componentes.absolutos[index].data = response.body;
                        _self.componentes.absolutos[index].permission = response.body.permission;
                        _self.componentes.absolutos[index].loading = false;
                        _self.$nextTick(function () {
                            _self.tooltip();
                        });
                    }, function (response) {
                        console.error("Error trying to retrieve data on 'dashboard/api/'" + c.endpoint);
                    });
                });
            },
            loadTemporais: function() {
                var componentesTemporais = this.componentes.temporais;
                var _self = this;
                componentesTemporais.forEach(function(c,i) {
                    var index = i;
                    var params = {};

                    if(_self.filtros.temporal.dataInputStart) {
                        params.start = _self.filtros.temporal.dataInputStart;
                        if(_self.filtros.temporal.dataInputEnd) {
                            params.end = _self.filtros.temporal.dataInputEnd;
                        } else {
                            params.end = _self.filtros.temporal.dataAplicada;
                        }
                    } else {
                        params.end = _self.filtros.temporal.dataAplicada;
                    }

                    _self.componentes.temporais[index].loading = true;
                    _self.$http.get('dashboard/api/' + c.endpoint, {
                        params: params
                    }).then(function (response) {
                        _self.componentes.temporais[index].data = response.body;
                        _self.componentes.temporais[index].permission = response.body.permission;
                        _self.componentes.temporais[index].loading = false;
                        var _s = _self;

                        _self.$nextTick(function() {
                            if(_s.componentes.temporais[index].type == 'serial') {
                                _s.makeSerialChart(_s.componentes.temporais[index].divId, _s.componentes.temporais[index].data);
                            } else if (_s.componentes.temporais[index].type == 'pie') {
                                _s.makePieChart(_s.componentes.temporais[index].divId, _s.componentes.temporais[index].data);
                            } else if (_s.componentes.temporais[index].type == 'line') {
                                _s.makeLineChart(_s.componentes.temporais[index].divId, _s.componentes.temporais[index].data);
                            } else {
                                _s.$set(_s.componentes.temporais[index], 'scrollable', false);
                            }
                        });
                    }, function (response) {
                        console.error("Error trying to retrieve data on 'dashboard/api/'" + c.endpoint);
                    });
                });
            },
            componentSize: function(c) {
                var size = [
                    'col-sm-' + c.size
                ];

                return size;
            },
            dataFiltro: function(dataAplicada) {
                if(!dataAplicada) {
                    var currentdate = new Date();
                    return [
                        (('0' + (currentdate.getDate())).slice(-2)),
                        (('0' + (currentdate.getMonth()+1)).slice(-2)),
                        currentdate.getFullYear()
                    ].join('/');
                }

                return dataAplicada;
            },
            copyTable: function(c){
                if(this.copyButton) {
                    this.copyButton.destroy();
                }
                this.copyButton = new ClipboardJS('#' + c.divId + '-copy');
            },
            load: function() {
                this.loadDiarios();
                this.loadAbsolutos();
                this.loadTemporais();
                var _self = this;

            },
            enableScroll: function(ct) {
                ct.scrollable = true;
            },
            isScrollEnabled: function(ct) {
                if(typeof ct.scrollable === 'undefined') {
                    return true;
                }

                return ct.scrollable;
            },
            makeSerialChart: function(divId, data) {
                var defaultOptions = {
                    "type": "serial",
                    "theme": "light",
                    "fontFamily": "Open Sans",
                    "dataProvider": data.items || data,
                    "valueAxes": [ {
                        "gridColor": "#FFFFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0
                    } ],
                    "gridAboveGraphs": true,
                    "startDuration": 1,
                    "graphs": [ {
                        "balloonText": "[[category]]: <b>[[value]]</b>",
                        "valueField": "valor",
                        "fillAlphas": 0.8,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "cornerRadiusTop": 3
                    } ],
                    "chartCursor": {
                        "categoryBalloonEnabled": false,
                        "cursorAlpha": 0,
                        "zoomable": false
                    },
                    "categoryField": "nome",
                    "categoryAxis": {
                        "gridPosition": "start",
                        "gridAlpha": 0,
                        "tickPosition": "start",
                        "tickLength": 20,
                    },
                    "export": {
                        "enabled": true
                    }

                };
                var options = deepMerge(defaultOptions, _.clone(data.options));
                var chart = AmCharts.makeChart( divId, options);
            },
            makeLineChart: function(divId, data) {
                var defaultOptions = {
                    "type": "serial",
                    "theme": "light",
                    "fontFamily": "Open Sans",
                    "dataProvider": data.items || data,
                    "valueAxes": [ {
                        "gridColor": "#FFFFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0,
                        "labelsEnabled": true,
                    } ],
                    "gridAboveGraphs": true,
                    "startDuration": 1,
                    "graphs": [ {
                        "balloonText": "[[category]]: <b>[[value]]</b>",
                        "valueField": "valor",
                        "bullet": "round",
                        "bulletSize": 8,
                        "lineColor": "#32c5d2",
                        "lineThickness": 4,
                        "type": "smoothedLine",
                    } ],
                    "chartCursor": {
                        "categoryBalloonEnabled": false,
                        "cursorAlpha": 0,
                        "zoomable": false
                    },
                    "categoryField": "nome",
                    "categoryAxis": {
                        "gridPosition": "start",
                        "gridAlpha": 0,
                        "tickPosition": "start",
                        "tickLength": 20
                    },
                    "export": {
                        "enabled": true
                    }
                };

                var options = deepMerge(defaultOptions, _.clone(data.options));
                var chart = AmCharts.makeChart( divId, options);
            },
            makePieChart: function(divId, data, options) {
                var defaultOptions = {
                    "type": "pie",
                    "theme": "light",
                    "fontFamily": "Open Sans",
                    "dataProvider": data.items || data,
                    "valueField": "valor",
                    "titleField": "nome",
                    "balloon":{
                        "fixedPosition":true
                    },
                    "export": {
                        "enabled": true
                    }
                };
                var options = deepMerge(defaultOptions, _.clone(data.options));
                var chart = AmCharts.makeChart( divId, options);
            },
            tooltip: function() {
                jQuery('[data-toggle="tooltip"]').tooltip();
            }
        },
        computed: {

        },
        mounted: function () {
            this.load();

        }
    });
})();

function deepMerge($default, overwrite) {
    if(!overwrite) {
        return $default;
    }

    if(typeof overwrite === typeof {}) {
        for (var key in overwrite) {
            if (overwrite.hasOwnProperty(key)) {
                var value = overwrite[key];

                if(typeof value === typeof {}) {
                    _.merge($default[key], value);
                } else {
                    $default[key] = value;
                }
            }
        }
    }

    return $default;
}

$(function() {

    var start = moment();
    var end = moment();
    var defaultRangepickerOptions = {
        startDate: start.startOf('month'),
        endDate: end.endOf('month'),
        locale: {
            customRangeLabel: "Escolher datas",
            cancelLabel: 'Cancelar',
            applyLabel: 'Aplicar',
            format: 'DD/MM/YYYY'
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Último mês': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    };

    function cb_diario(start, end) {
        $('#dashboard-range-diario span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    function cb_absoluto(start, end) {
        console.log(start, end);
        $('#dashboard-range-absoluto span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    function cb_temporal(start, end) {
        $('#dashboard-range-temporal span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    $('#dashboard-range-diario').daterangepicker({
        startDate: moment().subtract(1, 'days'),
        endDate: moment().subtract(1, 'days'),
        singleDatePicker: true,
        maxDate: moment().subtract(1, 'days'),
        ranges: {
            // 'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Semana passada': [moment().subtract(1, 'week'), moment().subtract(1, 'week')],
            'Mês passado': [moment().subtract(1, 'month'), moment().subtract(1, 'month')],
            'Ano passado': [moment().subtract(1, 'year'), moment().subtract(1, 'year')],
        },
        locale: {
            customRangeLabel: "Escolher data",
            cancelLabel: 'Cancelar',
            applyLabel: 'Aplicar',
            format: 'DD/MMM/YYYY'
        },
    }, cb_diario);
    $('#dashboard-range-absoluto').daterangepicker(defaultRangepickerOptions, cb_absoluto);
    $('#dashboard-range-temporal').daterangepicker(defaultRangepickerOptions, cb_temporal);

    cb_diario(moment().subtract(1, 'days'), moment().subtract(1, 'days'));
    cb_absoluto(start, end);
    cb_temporal(start, end);

    $('.dashboard-range').on('apply.daterangepicker', function(ev, picker) {

        var filtro = $(this).data('filtro');

        if(filtro === 'diario') {
            picker.endDate = picker.startDate;
        }

        var startDate = picker.startDate.format('DD/MM/YYYY');
        var endDate = picker.endDate.format('DD/MM/YYYY');

        window.vueDashboard.filtros[filtro].dataInputStart = startDate;
        window.vueDashboard.filtros[filtro].dataInputEnd = endDate;

        if(filtro === 'temporal') {
            window.vueDashboard.loadTemporais();
        } else if(filtro === 'absoluto') {
            window.vueDashboard.loadAbsolutos();
        } else {
            window.vueDashboard.loadDiarios();
        }
    });

});