<div id="layout-modal" class="layout-modal modal fade show" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Select Layout') }}</h4>
                <button type="button" class="" data-dismiss="modal">x</button>
            </div>
            <div class="modal-body py-3">
                <div class="py-3" id="section_layout">
                    <div class="row px-3">
                        {{-- 12 --}}
                        <div class="form-check col-3 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout" id="12"
                                value="12">
                            <label class="form-check-label" for="12">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M100,0V50H0V0Z"></path>
                                </svg>
                            </label>
                            <small>12</small>
                        </div>
                        {{-- 6 6 --}}
                        <div class="form-check col-3 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout" id="6_6"
                                value="6_6">
                            <label class="form-check-label" for="6_6">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M49,0V50H0V0Z M100,0V50H51V0Z"></path>
                                </svg>
                            </label>
                            <small>6,6</small>
                        </div>
                        {{-- 7 5 --}}
                        <div class="form-check col-3 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout" id="7_5"
                                value="7_5">
                            <label class="form-check-label" for="7_5">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M56.3,0V50H0V0Z M100,0V50H58.3V0Z"></path>
                                </svg>
                            </label>
                            <small>7,5</small>
                        </div>
                        {{-- 5 7 --}}
                        <div class="form-check col-3 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout" id="5_7"
                                value="5_7">
                            <label class="form-check-label" for="5_7">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M38,0V50H0V0Z M100,0V50H40V0Z"></path>
                                </svg>
                            </label>
                            <small>5,7</small>
                        </div>
                        {{-- 8 4 --}}
                        <div class="form-check col-3 p-0 mt-1 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout" id="8_4"
                                value="8_4">
                            <label class="form-check-label" for="8_4">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M65.3333,0V50H0V0Z M100,0V50H67.3333V0Z"></path>
                                </svg>
                            </label>
                            <small>8,4</small>
                        </div>
                        {{-- 4 8 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout" id="4_8"
                                value="4_8">
                            <label class="form-check-label" for="4_8">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M32.6667,0V50H0V0Z M100,0V50H34.6667V0Z"></path>
                                </svg>
                            </label>
                            <small>4,8</small>
                        </div>
                        {{-- 9 3 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout" id="9_3"
                                value="9_3">
                            <label class="form-check-label" for="9_3">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M75,0V50H0V0Z M100,0V50H77V0Z"></path>
                                </svg>
                            </label>
                            <small>9,3</small>
                        </div>
                        {{-- 3 9 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout" id="3_9"
                                value="3_9">
                            <label class="form-check-label" for="3_9">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M25,0V50H0V0Z M100,0V50H27V0Z"></path>
                                </svg>
                            </label>
                            <small>3,9</small>
                        </div>
                        {{-- 6 3 3 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout"
                                id="6_3_3" value="6_3_3">
                            <label class="form-check-label" for="6_3_3">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M48,0V50H0V0Z M74,0V50H50V0Z M100,0V50H76V0Z"></path>
                                </svg>
                            </label>
                            <small>6,3,3</small>
                        </div>
                        {{-- 3 3 6 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout"
                                id="3_3_6" value="3_3_6">
                            <label class="form-check-label" for="3_3_6">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M24,0V50H0V0Z M50,0V50H26V0Z M100,0V50H52V0Z"></path>
                                </svg>
                            </label>
                            <small>3,3,6</small>
                        </div>
                        {{-- 3 6 3 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout"
                                id="3_6_3" value="3_6_3">
                            <label class="form-check-label" for="3_6_3">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M24,0V50H0V0Z M74,0V50H26V0Z M100,0V50H76V0Z"></path>
                                </svg>
                            </label>
                            <small>3,6,3</small>
                        </div>
                        {{-- 4 4 4 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout"
                                id="4_4_4" value="4_4_4">
                            <label class="form-check-label" for="4_4_4">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M32,0V50H0V0Z M66,0V50H34V0Z M100,0V50H68V0Z"></path>
                                </svg>
                            </label>
                            <small>4,4,4</small>
                        </div>
                        {{-- 2 4 6 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout"
                                id="2_4_6" value="2_4_6">
                            <label class="form-check-label" for="2_4_6">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M15.66,0V50H0V0Z M50,0V50H17.66V0Z M100,0V50H52V0Z"></path>
                                </svg>
                            </label>
                            <small>2,4,6</small>
                        </div>
                        {{-- 2 7 3 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout"
                                id="2_7_3" value="2_7_3">
                            <label class="form-check-label" for="2_7_3">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M15.66,0V50H0V0Z M75,0V50H17.66V0Z M100,0V50H77V0Z"></path>
                                </svg>
                            </label>
                            <small>2,7,3</small>
                        </div>
                        {{-- 5 3 4 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout"
                                id="5_3_4" value="5_3_4">
                            <label class="form-check-label" for="5_3_4">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M40,0V50H0V0Z M65,0V50H42V0Z M100,0V50H67V0Z"></path>
                                </svg>
                            </label>
                            <small>5,3,4</small>
                        </div>
                        {{-- 3 3 3 3 --}}
                        <div class="form-check col-3 mt-1 p-0 text-center">
                            <input class="form-check-input d-none" type="radio" name="section_layout"
                                id="3_3_3_3" value="3_3_3_3">
                            <label class="form-check-label" for="3_3_3_3">
                                <svg xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 50">
                                    <path d="M23.5,0V50H0V0Z M49,0V50H25.5V0Z M74.5,0V50H51V0Z M100,0V50H76.5V0Z">
                                    </path>
                                </svg>
                            </label>
                            <small>3,3,3,3</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
