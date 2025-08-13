/**
 * Polling Utility for Real-time Data Updates
 * Provides automatic data refresh functionality for various components
 */

class PollingManager {
    constructor() {
        this.pollers = new Map();
        this.isVisible = true;
        this.setupVisibilityHandlers();
    }

    /**
     * Start a polling operation
     * @param {string} name - Unique name for this poller
     * @param {Function} callback - Function to execute on each poll
     * @param {number} interval - Polling interval in milliseconds (default: 30000ms = 30s)
     * @param {boolean} immediate - Execute immediately on start (default: false)
     */
    start(name, callback, interval = 30000, immediate = false) {
        // Stop existing poller with same name
        this.stop(name);

        console.log(`Starting poller: ${name} with ${interval}ms interval`);

        const poller = {
            callback,
            interval,
            timeoutId: null,
            lastRun: 0,
            isActive: false
        };

        this.pollers.set(name, poller);

        if (immediate) {
            this.executePoll(name);
        }

        this.scheduleNext(name);
    }

    /**
     * Stop a polling operation
     * @param {string} name - Name of the poller to stop
     */
    stop(name) {
        const poller = this.pollers.get(name);
        if (poller) {
            console.log(`Stopping poller: ${name}`);
            if (poller.timeoutId) {
                clearTimeout(poller.timeoutId);
            }
            poller.isActive = false;
            this.pollers.delete(name);
        }
    }

    /**
     * Stop all active pollers
     */
    stopAll() {
        console.log('Stopping all pollers');
        this.pollers.forEach((_, name) => this.stop(name));
    }

    /**
     * Pause polling when tab is not visible
     */
    pauseAll() {
        console.log('Pausing all pollers (tab not visible)');
        this.pollers.forEach(poller => {
            if (poller.timeoutId) {
                clearTimeout(poller.timeoutId);
                poller.timeoutId = null;
            }
        });
    }

    /**
     * Resume polling when tab becomes visible
     */
    resumeAll() {
        console.log('Resuming all pollers (tab visible)');
        this.pollers.forEach((_, name) => {
            this.scheduleNext(name);
        });
    }

    /**
     * Execute a poll immediately
     * @param {string} name - Name of the poller
     */
    async executePoll(name) {
        const poller = this.pollers.get(name);
        if (!poller || poller.isActive) return;

        poller.isActive = true;
        poller.lastRun = Date.now();

        try {
            await poller.callback();
        } catch (error) {
            console.error(`Polling error for ${name}:`, error);
        } finally {
            poller.isActive = false;
            
            // Schedule next poll if poller still exists
            if (this.pollers.has(name)) {
                this.scheduleNext(name);
            }
        }
    }

    /**
     * Schedule next poll execution
     * @param {string} name - Name of the poller
     */
    scheduleNext(name) {
        const poller = this.pollers.get(name);
        if (!poller) return;

        // Don't schedule if tab is not visible
        if (!this.isVisible) return;

        poller.timeoutId = setTimeout(() => {
            this.executePoll(name);
        }, poller.interval);
    }

    /**
     * Setup page visibility change handlers
     */
    setupVisibilityHandlers() {
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            this.isVisible = !document.hidden;
            
            if (this.isVisible) {
                // Resume polling when tab becomes visible
                setTimeout(() => this.resumeAll(), 1000); // Small delay to ensure page is ready
            } else {
                // Pause polling when tab is hidden
                this.pauseAll();
            }
        });

        // Handle page unload
        window.addEventListener('beforeunload', () => {
            this.stopAll();
        });

        // Handle page focus/blur for additional reliability
        window.addEventListener('focus', () => {
            if (this.isVisible) {
                this.resumeAll();
            }
        });

        window.addEventListener('blur', () => {
            this.pauseAll();
        });
    }

    /**
     * Get status of all pollers
     */
    getStatus() {
        const status = {};
        this.pollers.forEach((poller, name) => {
            status[name] = {
                interval: poller.interval,
                lastRun: poller.lastRun,
                isActive: poller.isActive,
                timeSinceLastRun: Date.now() - poller.lastRun
            };
        });
        return status;
    }
}

// Create global polling manager instance
window.pollingManager = new PollingManager();

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PollingManager;
}