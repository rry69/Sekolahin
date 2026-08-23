/**
 * Notification panel (ala Facebook) untuk dashboard siswa.
 * Didefinisikan sebagai global agar tersedia sebelum Alpine.start()
 * mengevaluasi x-data="notificationPanel({...})" pada komponen
 * <x-notification-panel>.
 */
window.notificationPanel = function (initial) {
    return {
        open: initial.open ?? false,
        unread: initial.unread ?? 0,
        notifications: initial.notifications ?? [],
        readUrl: initial.readUrl ?? null,
        markAllUrl: initial.markAllUrl ?? null,
        openUrl: initial.openUrl ?? null,

        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
        csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },

        /**
         * Tandai satu notifikasi sebagai dibaca (optimis, tetap di halaman).
         */
        async markRead(id) {
            const notif = this.notifications.find((n) => n.id === id);
            if (notif && !notif.read_at) {
                notif.read_at = new Date().toISOString();
                this.unread = Math.max(0, this.unread - 1);
            }
            try {
                await fetch((this.readUrl ?? `/notifications/${id}/read`).replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                        Accept: 'application/json',
                    },
                });
            } catch (e) {
                if (notif && notif.read_at) {
                    notif.read_at = null;
                    this.unread += 1;
                }
            }
        },

        /**
         * Tandai semua sebagai dibaca (optimis, tetap di halaman).
         */
        async markAllRead() {
            const prev = this.notifications.map((n) => ({ id: n.id, read_at: n.read_at }));
            this.notifications.forEach((n) => {
                n.read_at = new Date().toISOString();
            });
            this.unread = 0;
            try {
                await fetch(this.markAllUrl ?? '/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                        Accept: 'application/json',
                    },
                });
            } catch (e) {
                this.notifications.forEach((n) => {
                    const p = prev.find((x) => x.id === n.id);
                    if (p) n.read_at = p.read_at;
                });
                this.unread = this.notifications.filter((n) => !n.read_at).length;
            }
        },

        /**
         * Buka notifikasi yang punya tujuan: route notifications.open
         * menandai dibaca + redirect ke halaman tujuan.
         * (href anchor sudah menuju route ini — method disediakan untuk
         *  pemakaian programatik bila diperlukan.)
         */
        openNotification(id) {
            const url = (this.openUrl ?? `/notifications/${id}/open`).replace(':id', id);
            if (url) {
                window.location.href = url;
            }
        },
    };
};
