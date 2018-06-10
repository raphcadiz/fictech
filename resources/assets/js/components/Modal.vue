<!-- template for the modal component -->
<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-main-container">
            <div class="modal-dialog" :class="[modalSize, modalType]">

              <div class="modal-content"  >

                  <div class="modal-header">
                    <slot name="header">
                      <h5 slot="header" style="margin-bottom:0">INFORMATION</h5>
                    </slot>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="$emit('close')">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>

                  <div class="modal-body " >
                    <slot name="body" />
                  </div>
              </div>

            </div>
        </div>

      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    name: 'VModal',
    props: {
      modalSize: {
        type: String
      },
      modalType: {
        type: String
      }
    }
  }
</script>

<style>
 .modal-mask {
  position: absolute;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: grid;
  transition: opacity .3s ease;
  bottom: 0;
}

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-dialog,
.modal-leave-active .modal-dialog {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}

.modal-main-container{
  position:fixed;
  width:100%;
  overflow: auto;
  height: 100vh;
}

</style>