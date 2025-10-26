
export default function alertComponent() {
    return {
        init() {
            // Debugging
            console.log('Alert component initialized');
            console.log('Alert store exists:', !!Alpine.store('alert'));
        },
        alert: Alpine.store('alert')
    };
}