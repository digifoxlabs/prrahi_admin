<div 
  x-data="{
    visible: false,
    message: '',
    type: 'success',
    show(msg, msgType = 'success') {
      this.message = msg;
      this.type = msgType;
      this.visible = true;
      setTimeout(() => this.visible = false, 3000);
    },
    init() {
      window.toast = (msg, type = 'success') => this.show(msg, type);
    }
  }"
  x-init="init()"
  x-show="visible"
  x-transition
  x-cloak
  class="fixed top-6 right-6 z-50 px-4 py-3 rounded shadow-md"
  :class="type === 'success' 
    ? 'bg-green-100 border border-green-400 text-green-800' 
    : 'bg-red-100 border border-red-400 text-red-800'"
  x-text="message"
>
</div>
