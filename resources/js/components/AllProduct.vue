<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-5">
                <div>
                    <label class = "inline mr-5" for = "name"><h1>Fileter By : </h1></label>
                    <label class = "checkbox-inline">
                    <input
                     type = "checkbox"  
                     v-model="filterByDate" true-value="1" false-value="0">  
                     Expired Date
                     </label>
                     <label class = "col-md-8 pull-right checkbox-inline">
                        <fieldset class="border border-primary p-2">
                           <legend  class="w-auto">Price Range:</legend>
                           <div class="row">
                            <div class="col-md-8 pt-2">
                               Min <input type="number" v-model="pricevalue[0]">
                               Max <input type="number" v-model="pricevalue[1]">
                                
                            </div>
                            <div class="col-md-4">
                                <button type="submit" @click.prevent="priceByFilter()"  class="btn btn-primary"><i class="fa fa-filter"></i></button>
                                 <button v-if="filterMode" @click.prevent="reset()" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                            </div>
                           </div>
                        </fieldset>
                      </label>
                </div>
            </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="row">
                	<div class="col-md-8">
                		<h3 class="card-title">Product Lists</h3>
                	</div>
                	<div class="col-md-4">
                		<div class="card-tools pull-right">
                    	<button class="btn btn-success" @click="newModal"><i class="fa fa-plus"></i></button>
                	</div>
                	</div>
                	
                </div>

                
              </div>
             
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                	 <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Detail</th>
                <th>Price</th>
                <th>Expired Date</th>
                <th width="2%">Actions</th> 
            </tr>
            </thead>
            <tbody>
            <tr v-for="(product, index) in products.data" :key="product.id">
                <td width="15%">
                        <img  :src="getProductImg(product.image)" class="img-fluid"/>
                   </td>
                <td>{{ product.name | upText}}</td>
                <td>{{ product.detail }}</td>
                <td>{{ product.price }}</td>
                <td>{{ product.expired_date |formateDate}}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button @click.prevent="editModal(product)" class="btn btn-success mr-2"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-danger" @click.prevent="product_delete(product.id)"><i class="fa fa-trash"></i></button>
                    </div>
                </td>
            </tr>
            <tr v-if="products.data==''">
            	<td colspan="6"><h2 class="text-center">Product Not Found</h2></td>
            </tr>
            </tbody>
                </table>
              </div>
            
              <div class="card-footer">
                 <pagination :data="products" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
           
          </div>
        </div>



	<!-- Modal -->
	<div class="modal fade" id="addNew" tabindex="-1" role="dialog"aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
                <h5 v-show="!editMode" class="modal-title" id="exampleModalLabel">Add New Product</h5>
		        <h5 v-show="editMode" class="modal-title" id="exampleModalLabel">Update Product</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
	      	</div>
	      	<form @submit.prevent="editMode ? product_update():product_add()">
	      		<div class="modal-body">
			        <div class="form-group">
					    <label >Product Name</label>
					    <input v-model="form.name" type="text" name="name"
					        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
					    <has-error :form="form" field="name"></has-error>
				    </div>
				    <div class="form-group">
					    <label>Price</label>
					    <input v-model="form.price" type="text" name="price"
					        class="form-control" :class="{ 'is-invalid': form.errors.has('price') }">
					    <has-error :form="form" field="price"></has-error>
				    </div>
				    <div class="form-group">
					    <label>Image</label>
					    <img width="100%" height="50%" :src="imageUpdateMode? 'http://localhost:8000/images/products/'+form.image : form.image" alt="" class="img-fluid">
					   <div v-if="!form.image" class="custom-file">
						    <input @change="FileChangeEvent" type="file" name="image"
						        class="form-control" :class="{ 'is-invalid': form.errors.has('image') }">
						    <has-error :form="form" field="image"></has-error>
						 </div>
                        <div v-else>
                            <button @click="RemoveImage" type="button" class="btn btn-danger mt-2">Change image</button>
                        </div>
				    </div>
				    <div class="form-group">
					    <label>Product Expired Date</label>
					    <input v-model="form.expired_date" type="date" name="expired_date"
					        class="form-control" :class="{ 'is-invalid': form.errors.has('expired_date') }">
					    <has-error :form="form" field="expired_date"></has-error>
				    </div>
				    <div class="form-group">
			            <label for="message-text" class="col-form-label">Details:</label>
			            <textarea v-model="form.detail" class="form-control" :class="{ 'is-invalid': form.errors.has('detail') }"></textarea>
			            <has-error :form="form" field="detail"></has-error>
			        </div>
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        	<button v-if="editMode" type="submit" class="btn btn-primary">Product Update</button>
                    <button v-else type="submit" class="btn btn-primary">Product Add</button>
		      	</div>
	      </form>
	    </div>
	  </div>
	</div>
          
</div>

</template>
<script>
// https://stackoverflow.com/questions/54435670/how-to-sort-price-and-date-by-vue-in-laravel
    export default {
        data() {
            return {
                products: {},
                filterMode:false,
                filterByDate:0,
                pricerange:[],
                pricevalue: [],
                editMode:false,
                imageUpdateMode:false,
                form: new Form({
                	id:'',
			        name: '',
			        price: '',
			        image: '',
			        expired_date:'',
			        detail: ''
			    })
            }
        },
        created() {
            this.get_all_product();
            this.priceRange();
            
        },
        computed:{
            //  one: function () {

            //     return this.filterByDate ? this.filterExpiredData(): this.get_all_product();
            // }
        },
         watch:{
            filterByDate(after,before){
                this.filterExpiredData();
            },
            // pricevalue(after,before){
            //     this.priceByFilter();
            // }
           
        },
        methods: {
            get_all_product(){
	           axios.get('api/products')
	            .then(response =>{
	                // handle success
	                this.products = response.data;
	            })
	            .catch(function (error) {
	                // handle error
	                console.log(error);
	            })
	            .finally(function () {
	                // always executed
	            }); 
	        },
            reset(){
                this.pricevalue=[];
                this.filterMode=false;
                this.get_all_product();
            },
            filterExpiredData(){
                axios.get('api/products/'+this.filterByDate)
                .then(response =>{
                    this.products = response.data;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
            },
            priceRange(){
                axios.get('api/price-range')
                .then(response =>{
                    // handle success
                    this.pricerange = [response.data[0].min,response.data[0].max];
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
            },
            priceByFilter(){
                console.log('sds');
                  this.filterMode=true;
                axios.get('api/price-filter/'+this.pricevalue)
                .then(response =>{
                    // handle success
                    this.products = response.data;
                    //this.pricerange = [response.data[0].min,response.data[0].max];
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
            },
            product_add(){
            	this.$Progress.start();
	            this.form.post('api/products')
	            .then(({ data }) =>{
	                this.get_all_product();
	                $('#addNew').modal('hide')
	                 toast.fire({
                      icon: 'success',
                      title: 'Record created successfully'
                    });
	                this.$Progress.finish();
	            })
	            .catch(({err}) => {
                    this.$Progress.fail();
                })
	        },
	        product_update(){
	        	this.$Progress.start();
              	this.form.put('api/products/'+this.form.id)
              	.then((res)=>{
	                  $('#addNew').modal('hide');
	                  toast.fire(
	                   	{
	                   		icon: 'success',
                      		title: 'Record updated successfully'
	                   	}
	                  )
	                  this.$Progress.finish();
	                  this.get_all_product();
              	})
              	.catch(()=>{
                	this.$Progress.fail();
              	})
	        },
	        product_delete(product_id){
	             let ref=this;
	             iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode:'once',
                    id:'question',
                    zindex:999,
                    title:'Hey',
                    message:'Are you sure to delete that?',
                    position:'center',
                    buttons:[
                    ['<button><b>Yes</b></button>',function(instance,toast) {
                        instance.hide({transitionOut:'fadeOut'},toast,'button');
                        ref.form.delete("api/products/"+product_id)
                            .then(response =>{
                                 console.log(response);
                                ref.get_all_product();
                                iziToast.success({
                                    title: 'Success',
                                    message: response.data.success,
                                });
                            })
                            .catch(err=>{console.log(err);})
                    },true],
                    ['<button><b>Cancel</b></button>',function(instance,toast) {
                        instance.hide({transitionOut:'fadeOut'},toast,'button');
                    }],
                    ],
                });
	        },
	        getResults(page = 1) {
	            axios.get('api/products?page=' + page)
	              .then(response => {
	                this.products = response.data;
	            });
	        },
	        editModal(product){
                this.editMode = true;
                this.imageUpdateMode = true;
                this.resetModal();
                this.form.fill(product);
            },
            newModal(){
                this.editMode = false;
                this.resetModal();
            },
            resetModal(){
            	this.form.reset();
                this.form.clear();
                $('#addNew').modal('show');
            },
             RemoveImage(){
	            this.imageUpdateMode = false;
	            this.form.image = '';
	        },
            getProductImg(img){
                let photo = (img=='default.jpg') ? "http://localhost:8000/images/"+ img  : "http://localhost:8000/images/products/"+ img ;
                return photo;
            },
            FileChangeEvent(e){
	            var file = e.target.files[0];
	            const reader = new FileReader();
	            // console.log(file['size']);
	            if(file['size'] < 2097152 ){
	                 reader.onload = ()=>{
	                    this.form.image = reader.result
	                    iziToast.success({
	                        title: 'success',
	                        message: 'product Image is Ok = '+file['size'] / 1048576 +"MB",
	                    });
	                }
	                reader.readAsDataURL(file);   
	            }else{
	                iziToast.error({
	                    title: 'Warning',
	                    message: 'product Image is large = '+file['size'] / 1048576 +"MB",
	                });
	            }
	            
	        }
        }
    }
</script>