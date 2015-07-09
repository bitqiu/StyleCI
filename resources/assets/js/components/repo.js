var RepoList = Vue.extend({
    ready: function() {
        this.repoId = $('#repo').data('id');
        this.repoBranch = $('#repo').data('branch');
        this.getAnalyses();
        this.getBranches();
        this.subscribe();
    },
    methods: {
        getBranches: function() {
            var self = this;
            var url = StyleCI.globals.base_url + '/api/repos/' + self.repoId + '/branches';

            return $.get(url)
                .done(function(response) {
                    self.$set('branches', response.data);
                })
                .fail(function(response) {
                    (new StyleCI.Notifier()).notify(response.responseJSON.errors[0].title);
                })
                .always(function() {
                    self.isLoading = false;
                });
        },
        getAnalyses: function() {
            var self = this;
            var url = StyleCI.globals.base_url + '/api/repos/' + self.repoId + '?branch=' + self.repoBranch + '&page=' + self.currentPage;

            return $.get(url)
                .done(function(response) {
                    self.$set('analyses', response.data);
                    self.$set('currentPage', response.pagination.current_page);
                    self.$set('totalPages', response.pagination.total_pages);
                })
                .fail(function(response) {
                    (new StyleCI.Notifier()).notify(response.responseJSON.errors[0].title);
                })
                .always(function() {
                    self.isLoading = false;
                });
        },
        analyseRepo: function(e) {
            e.preventDefault();
            var self = this;
            var btn = $(e.target);
            var url = StyleCI.globals.base_url + '/api/repos/' + self.repoId + '/analyse?branch=' + self.repoBranch;

            btn.button('loading');

            return $.post(url)
                .fail(function(response) {
                    (new StyleCI.Notifier()).notify(response.responseJSON.errors[0].title);
                })
                .always(function() {
                    btn.button('reset').blur();
                });
        },
        filterBranch: function(branch) {
            if (this.repoBranch == branch) {
                return;
            }

            this.repoBranch = branch;
            this.isLoading = true;
            this.getAnalyses();
        },
        pageForward: function() {
            if (this.totalPages <= this.currentPage) {
                return;
            }

            this.currentPage++;
            this.isLoading = true;
            this.getAnalyses();
        },
        pageBackward: function() {
            if (this.totalPages > this.currentPage) {
                return;
            }

            this.currentPage--;
            this.isLoading = true;
            this.getAnalyses();
        },
        AnalysisStatusChangeEventHandler: function(data) {
            var self = this;
            if (data.event.branch != self.repoBranch) {
                return;
            }

            var repo = _.findWhere(this.repos, { 'id': data.event.id });

            if (repo) {
                repo = data.event;
            } else {
                this.getAnalyses();
            }
        },
        subscribe: function() {
            var self = this;
            StyleCI.RealTime.getChannel('repo-' + self.repoId).bind(
                'AnalysisStatusUpdatedEvent',
                self.AnalysisStatusChangeEventHandler
            );
        }
    },
    data: function() {
        return {
            repoId: null,
            repoBranch: null,
            isLoading: true,
            currentPage: 1,
            totalPages: 1,
            search: '',
            analyses: [],
            branches: []
        };
     }
});

Vue.component('sc-repo', RepoList);
