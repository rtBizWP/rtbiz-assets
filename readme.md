rtBiz Assets
============

The objective of this plugin is to help a business in its assets/hardware/inventory management.

### Fields required: 

- Device ID	
- Device Type (CPU, RAM, CD ROM, Mother Board, Keyboard, Mouse, Phone, iPad, Laptop, Router, Printer)	
- Serial Number	
- Device Name	
- Current User (Assigned To) 	
- Vendor 	
- Invoice Number	
- Purchase Date	
- Warranty 
- Expiry Date	
- Comments
- Device Image 


### Device Status

Post status to be used for representing Device status. List below:

- Assigned - Device healthy and is assigned to a user.
- Unassigned - Device healthy and is not assigned to any user.
- Faulty/Broken - Unassigned Device, not healthy, needs replacement
- NeedFix - Assigned Device, not healthy, needs replacement
- Expired - Device is out of warranty


### Notes:

- `Current user` will be the author of the post.
- Category for device types - Taxanomy. 
- Invetory management. 
- `Vendor` & `Current User` will be mapped to rt-Biz contacts. 
- CPT support for featured image and comments. 
- `Device ID` will be post ID.  
- Find a solution for bundling. 
	- Post Hierchy to be used for bundling. 
	- A device, e.g. Hard disk, can be made child post of a Cabinet Parent post. 