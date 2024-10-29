<nav id="Bottom-nav" class="fixed bottom-0 w-full max-w-[640px] mx-auto border-t border-[#E9E8ED] p-[20px_24px] bg-white z-20">
    <ul class="flex items-center justify-evenly">
      <li>
        <a href="{{ route("front.index") }}" class="flex flex-col items-center text-center gap-1">
          <div class="w-6 h-6 flex shrink-0 ">
            <img src="{{ asset("assets/images/icons/element-equal.svg") }}" alt="icon">
          </div>
          <p class="font-semibold text-xs leading-[18px] text-[#FF8969]">Home</p>
        </a>
      </li>
      <li>
        <a href="{{ route("front.transactions") }}" class="flex flex-col items-center text-center gap-1">
          <div class="w-6 h-6 flex shrink-0 ">
            <img src="{{ asset("assets/images/icons/element-equal-grey.svg") }}" alt="icon">
          </div>
          <p class="font-semibold text-xs leading-[18px] text-[#BABEC7]">Orders</p>
        </a>
      </li>
      <li>
        <a href="#" class="flex flex-col items-center text-center gap-1">
          <div class="w-6 h-6 flex shrink-0 ">
            <img src="{{ asset("assets/images/icons/ticket-discount-grey.svg") }}" alt="icon">
          </div>
          <p class="font-semibold text-xs leading-[18px] text-[#BABEC7]">Coupons</p>
        </a>
      </li>
      <li>
        <a href="#" class="flex flex-col items-center text-center gap-1">
          <div class="w-6 h-6 flex shrink-0 ">
            <img src="{{ asset("assets/images/icons/message-question-grey.svg") }}" alt="icon">
          </div>
          <p class="font-semibold text-xs leading-[18px] text-[#BABEC7]">Help</p>
        </a>
      </li>
    </ul>
  </nav>
