

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.projectsCrud = (config) => ({
    projects: config.projects ?? [],
    selectedProjectId: config.selectedProjectId ?? null,
    selectedTaskId: config.selectedTaskId ?? null,
    canManageProject: config.canManageProject ?? true,
    successMessage: '',
    errorMessage: '',
    isSubmitting: false,
    confirmDialog: {
        open: false,
        type: null,
        id: null,
        title: '',
        message: '',
        confirmLabel: 'Delete',
    },
    createProjectForm: {
        project_name: '',
        project_description: '',
    },
    projectForm: {
        project_name: '',
        project_description: '',
    },
    createTaskForm: {
        task_name: '',
        task_description: '',
        task_completed: false,
    },
    taskForm: {
        task_name: '',
        task_description: '',
        task_completed: false,
    },
    init() {
        this.ensureSelection();
        this.syncForms();
    },
    get selectedProject() {
        return this.projects.find((project) => project.id === this.selectedProjectId) ?? null;
    },
    get selectedTask() {
        return this.selectedProject?.tasks.find((task) => task.id === this.selectedTaskId) ?? null;
    },
    completedProjectsCount() {
        return this.projects.filter((project) => this.isProjectComplete(project)).length;
    },
    pendingProjectsCount() {
        return this.projects.filter((project) => !this.isProjectComplete(project)).length;
    },
    isProjectComplete(project) {
        return project.tasks.length > 0 && project.tasks.every((task) => task.completed);
    },
    completedTaskCount(project) {
        return project.tasks.filter((task) => task.completed).length;
    },
    ensureSelection() {
        if (!this.projects.length) {
            this.selectedProjectId = null;
            this.selectedTaskId = null;
            return;
        }

        if (!this.projects.some((project) => project.id === this.selectedProjectId)) {
            this.selectedProjectId = this.projects[0].id;
        }

        const currentProject = this.selectedProject;

        if (!currentProject) {
            this.selectedTaskId = null;
            return;
        }

        if (!currentProject.tasks.some((task) => task.id === this.selectedTaskId)) {
            this.selectedTaskId = currentProject.tasks[0]?.id ?? null;
        }
    },
    syncForms() {
        this.projectForm.project_name = this.selectedProject?.name ?? '';
        this.projectForm.project_description = this.selectedProject?.description ?? '';

        this.taskForm.task_name = this.selectedTask?.name ?? '';
        this.taskForm.task_description = this.selectedTask?.description ?? '';
        this.taskForm.task_completed = this.selectedTask?.completed ?? false;
    },
    selectProject(projectId) {
        this.selectedProjectId = projectId;
        this.selectedTaskId = this.selectedProject?.tasks[0]?.id ?? null;
        this.syncForms();
    },
    selectTask(taskId) {
        this.selectedTaskId = taskId;
        this.syncForms();
    },
    openDeleteProjectDialog(projectId) {
        if (!this.canManageProject) {
            this.errorMessage = 'You can only view tasks for this project.';
            return;
        }
        this.confirmDialog = {
            open: true,
            type: 'project',
            id: projectId,
            title: 'Delete project?',
            message: 'This project and all of its tasks will be permanently removed.',
            confirmLabel: 'Delete project',
        };
    },
    openDeleteTaskDialog(taskId) {
        if (!this.canManageProject) {
            this.errorMessage = 'You can only view tasks for this project.';
            return;
        }
        this.confirmDialog = {
            open: true,
            type: 'task',
            id: taskId,
            title: 'Delete task?',
            message: 'This task will be permanently removed from the project.',
            confirmLabel: 'Delete task',
        };
    },
    closeConfirmDialog() {
        this.confirmDialog = {
            open: false,
            type: null,
            id: null,
            title: '',
            message: '',
            confirmLabel: 'Delete',
        };
    },
    async confirmDelete() {
        if (this.confirmDialog.type === 'project' && this.confirmDialog.id) {
            await this.deleteProject(this.confirmDialog.id);
        }

        if (this.confirmDialog.type === 'task' && this.confirmDialog.id) {
            await this.deleteTask(this.confirmDialog.id);
        }
    },
    async send(url, formData) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token ?? '',
            },
            body: formData,
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            if (response.status === 422) {
                const firstError = Object.values(payload.errors ?? {}).flat()[0];
                throw new Error(firstError || 'Validation failed.');
            }

            throw new Error(payload.message || 'Something went wrong.');
        }

        return payload;
    },
    applyPayload(payload) {
        this.projects = payload.projects ?? [];
        this.selectedProjectId = payload.selectedProjectId ?? this.selectedProjectId;
        this.selectedTaskId = payload.selectedTaskId ?? this.selectedTaskId;
        this.successMessage = payload.message ?? '';
        this.errorMessage = '';
        this.ensureSelection();
        this.syncForms();
    },
    async createProject() {
        this.isSubmitting = true;
        this.errorMessage = '';

        try {
            const formData = new FormData();
            formData.append('project_name', this.createProjectForm.project_name);
            formData.append('project_description', this.createProjectForm.project_description);

            const payload = await this.send(config.routes.projectStore, formData);
            this.applyPayload(payload);
            this.createProjectForm = { project_name: '', project_description: '' };
        } catch (error) {
            this.errorMessage = error.message;
        } finally {
            this.isSubmitting = false;
        }
    },
    async updateProject() {
        if (!this.selectedProject) return;
        if (!this.canManageProject) {
            this.errorMessage = 'You can only view this project.';
            return;
        }

        this.isSubmitting = true;
        this.errorMessage = '';

        try {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('project_name', this.projectForm.project_name);
            formData.append('project_description', this.projectForm.project_description);
            if (this.selectedTaskId) {
                formData.append('selected_task_id', String(this.selectedTaskId));
            }

            const payload = await this.send(`${config.routes.projectBase}/${this.selectedProject.id}`, formData);
            this.applyPayload(payload);
        } catch (error) {
            this.errorMessage = error.message;
        } finally {
            this.isSubmitting = false;
        }
    },
    async deleteProject(projectId) {
        if (!this.canManageProject) {
            this.errorMessage = 'You can only view this project.';
            return;
        }
        this.isSubmitting = true;
        this.errorMessage = '';

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const payload = await this.send(`${config.routes.projectBase}/${projectId}`, formData);
            this.closeConfirmDialog();
            if (config.redirectAfterProjectDelete) {
                window.location.href = config.routes.projectsIndex;
                return;
            }
            this.applyPayload(payload);
        } catch (error) {
            this.errorMessage = error.message;
        } finally {
            this.isSubmitting = false;
        }
    },
    async createTask() {
        if (!this.selectedProject) return;
        if (!this.canManageProject) {
            this.errorMessage = 'You can only view tasks for this project.';
            return;
        }

        this.isSubmitting = true;
        this.errorMessage = '';

        try {
            const formData = new FormData();
            formData.append('task_name', this.createTaskForm.task_name);
            formData.append('task_description', this.createTaskForm.task_description);

            if (this.createTaskForm.task_completed) {
                formData.append('task_completed', '1');
            }

            const payload = await this.send(`${config.routes.projectBase}/${this.selectedProject.id}/tasks`, formData);
            this.applyPayload(payload);
            this.createTaskForm = { task_name: '', task_description: '', task_completed: false };
        } catch (error) {
            this.errorMessage = error.message;
        } finally {
            this.isSubmitting = false;
        }
    },
    async updateTask() {
        if (!this.selectedTask) return;
        if (!this.canManageProject) {
            this.errorMessage = 'You can only view tasks for this project.';
            return;
        }

        this.isSubmitting = true;
        this.errorMessage = '';

        try {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('task_name', this.taskForm.task_name);
            formData.append('task_description', this.taskForm.task_description);

            if (this.taskForm.task_completed) {
                formData.append('task_completed', '1');
            }

            const payload = await this.send(`${config.routes.taskBase}/${this.selectedTask.id}`, formData);
            this.applyPayload(payload);
        } catch (error) {
            this.errorMessage = error.message;
        } finally {
            this.isSubmitting = false;
        }
    },
    async toggleTask(taskId) {
        if (!this.canManageProject) {
            this.errorMessage = 'You can only view tasks for this project.';
            return;
        }
        this.isSubmitting = true;
        this.errorMessage = '';

        try {
            const formData = new FormData();
            formData.append('_method', 'PATCH');

            const payload = await this.send(`${config.routes.taskBase}/${taskId}/toggle`, formData);
            this.applyPayload(payload);
        } catch (error) {
            this.errorMessage = error.message;
        } finally {
            this.isSubmitting = false;
        }
    },
    async deleteTask(taskId) {
        if (!this.canManageProject) {
            this.errorMessage = 'You can only view tasks for this project.';
            return;
        }
        this.isSubmitting = true;
        this.errorMessage = '';

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const payload = await this.send(`${config.routes.taskBase}/${taskId}`, formData);
            this.closeConfirmDialog();
            this.applyPayload(payload);
        } catch (error) {
            this.errorMessage = error.message;
        } finally {
            this.isSubmitting = false;
        }
    },
});

Alpine.start();
