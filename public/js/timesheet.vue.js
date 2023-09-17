+(function () {
    if (!document.getElementById('Timesheet')) {
        return;
    }
    //Vue.use(VueTheMask);

    window.vueTimesheet = new Vue({
        el: "#Timesheet",
        data: {
            api: 'timesheet/api',
            timer: {
                start:    null,
                interval: null
            },
            selection: {
                project:    null,
                task:       null,
                department: null,
                sheet: null
            },
            departments: [],
            projects:    [],
            tasks:       [],
            sheets:      [],
            record: {
                project: null,
                task: null
            },
            isSaving:    false,
            select2Elements: {
                departments: null,
                projects: null,
                tasks: null
            }
        },
        methods: {
            load: function () {
                this.loadDepartments();
                this.getCurrent();
                this.loadSheets();
                this.init();
            },
            init: function() {
                this.selects();
            },
            selects: function() {
                $('select.materialize').formSelect();

            },
            select2: function(placeholder, element) {
                if(this.select2Elements[element]) {
                    this.select2Elements[element].select2('destroy');
                }
                this.select2Elements[element] =  $('.select2-vue-'+element)
                    .select2({
                        placeholder: placeholder
                    })
                    .on('select2:select', function (e) {
                        this.dispatchEvent(new Event('change', { target: e.target }));
                    });
            },
            startTimer: function() {
                if(this.timer.interval) {
                    clearInterval(this.timer.interval);
                }
                var _self = this;

                $('#timer').html('00:00:00');
                this.timer.interval = setInterval(function(){
                    var start = _self.timer.start;
                    var end = moment();

                    // calculate the duration
                    var d = moment.duration(end.diff(start));

                    // format a string result
                    var s = moment.utc(+d).format('HH:mm:ss');
                    //var $time = $m.format('HH:mm:ss');

                    $('#timer').html(s);
                }, 1000);
            },
            stopTimer: function() {
                if(this.timer.interval) {
                    clearInterval(this.timer.interval);
                }
                this.timer.start = null;
                $('#timer').html('--:--:--');
            },
            loadDepartments: function() {
                var params = {};
                var _self = this;
                this.$http.get(this.api + '/departamentos', {
                    params: params
                }).then(function (response) {
                    _self.departments = response.body;
                    _self.$nextTick(function() {
                        _self.init();
                        _self.select2('DEPARTAMENTOS', 'departments');
                    });
                }, function (response) {
                    console.error(response);
                    console.error("Error trying to retrieve data on " + _self.api + '/departamentos');
                });
            },
            loadProjects: function($department, options) {
                if(typeof options === 'undefined') {
                    options = {
                        keepSelection: false
                    };
                }

                if(!$department) {
                    console.error("Can't retrieve PROJECTS without a DEPARTMENT set.");
                    return;
                }

                if(!options.keepSelection) {
                    this.selection.project = this.selection.task = null;
                }

                this.projects = this.tasks = [];

                var params = {
                    id_departamento: $department.id
                };
                var _self = this;
                this.$http.get(this.api + '/projetos', {
                    params: params
                }).then(function (response) {
                    _self.projects = response.body;
                    _self.$nextTick(function() {
                        _self.init();
                        _self.select2('PROJETOS', 'projects');
                    });
                }, function (response) {
                    console.error(response);
                    console.error("Error trying to retrieve data on " + _self.api + '/projetos');
                });
            },
            loadTasks: function($project, options) {
                if(typeof options === 'undefined') {
                    options = {
                        keepSelection: false
                    };
                }

                if(!$project) {
                    console.error("Can't retrieve TASKS without a PROJECT set.");
                    return;
                }

                if(!options.keepSelection) {
                    this.selection.task = null;
                }

                this.tasks = [];

                var params = {
                    id_projeto: $project.id
                };
                var _self = this;
                this.$http.get(this.api + '/tarefas', {
                    params: params
                }).then(function (response) {
                    _self.tasks = response.body;
                    _self.$nextTick(function() {
                        _self.init();
                        _self.select2('TAREFAS', 'tasks');
                    });
                }, function (response) {
                    console.error(response);
                    console.error("Error trying to retrieve data on " + _self.api + '/tarefas');
                });
            },
            loadSheets: function() {
                //this.sheets = [];
                var _self = this;
                var params = {};

                this.$http.get(this.api + '/historico', {
                    params: params
                }).then(function (response) {
                    _self.sheets = response.body;
                }, function (response) {
                    console.error(response);
                    console.error("Error trying to retrieve data on " + _self.api + '/historico');
                });
            },
            getCurrent: function() {
                var _self = this;
                this.$http.get(this.api + '/corrente').then(function(response) {
                    var current = response.body;
                    if(current.timesheet) {
                        _self.timer.start = current.timesheet.inicio;

                        var $department = _self.$set(_self.selection, 'department', current.department);
                        var $project = _self.$set(_self.selection, 'project', current.project);
                        var $task = _self.$set(_self.selection, 'task', current.task);

                        _self.$nextTick(function() {
                            var loadOptions = {
                                keepSelection: true
                            };
                            _self.loadProjects($department, loadOptions);
                            _self.loadTasks($project, loadOptions);
                            _self.init();
                        });

                        return _self.startTimer();
                    }
                }, function(response) {
                    console.error(response);
                });
            },
            departmentChanged: function() {
                return this.loadProjects(this.selection.department);
            },
            projectChanged: function() {
                return this.loadTasks(this.selection.project);
            },
            taskChanged: function() {

            },
            operate: function() {
                var _self = this;
                if(this.shouldStop) {
                    //Push end of current task;
                    return this.$http.post(this.api + '/parar', {}).then(function(response) {
                        this.loadSheets();
                        return _self.stopTimer();
                    }, function(response) {
                        console.error(response);
                    });
                }

                if(this.valid) {
                    this.$http.post(this.api + '/iniciar', {
                        "id_tarefa": this.selection.task.id
                    }).then(function(response) {
                        this.loadSheets();
                        return _self.getCurrent();
                    }, function(response) {
                        console.error(response);
                    });
                }
            },
            saveProject: function() {
                if(!this.selection.department ||
                   !this.record.project) {
                    return;
                }

                var _self = this;

                return this.$http.post(this.api + '/projeto', {
                    id_departamento: this.selection.department.id,
                    nome: this.record.project
                }).then(function(response) {
                    _self.loadProjects(_self.selection.department);
                    _self.closeModal('#Timesheet--modal-project');
                    _self.record.project = null;
                }, function(response) {
                    console.error(response);
                });
            },
            saveTask: function() {
                if(!this.selection.department ||
                   !this.selection.project ||
                   !this.record.task) {
                    return;
                }

                var _self = this;

                return this.$http.post(this.api + '/tarefa', {
                    id_projeto: this.selection.project.id,
                    nome: this.record.task
                }).then(function(response) {
                    _self.loadTasks(_self.selection.project);
                    _self.closeModal('#Timesheet--modal-task');
                    _self.record.task = null;
                }, function(response) {
                    console.error(response);
                });
            },
            openModal: function(modal) {
                var $modal = $(modal);
                if($modal.length > 0) {
                    $(modal).modal('show');
                }
            },
            closeModal: function(modal) {
                var $modal = $(modal);
                if($modal.length > 0) {
                    $modal.modal('hide');
                }
            },
            openEditSheet: function(sheet) {
                this.selection.sheet = sheet;
                this.$nextTick(function() {
                    this.openModal('#Timesheet--modal-sheet');
                });

            },
            editSheet: function() {
                var _self = this;

                return this.$http.post(this.api + '/timesheet', {
                    sheet: this.selection.sheet,
                }).then(function(response) {
                    _self.load();
                    _self.selection.sheet = null;
                    _self.closeModal('#Timesheet--modal-sheet');
                }, function(response) {
                    console.error(response);
                });
            }
        },
        computed: {
            valid: function() {
                return this.selection.department != null &&
                       this.selection.project != null &&
                       this.selection.task != null;
            },
            canPlay: function() {
                return this.valid &&
                       this.timer.start == null;
            },
            shouldStop: function() {
                return this.timer.start !== null;
            }
        },
        mounted: function () {
            this.load();
        }
    });
})();