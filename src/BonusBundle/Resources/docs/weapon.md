Create a weapon
=======


### Create PHP class

- Create a PHP class in `src/BonusBundle/Weapons`
- Extend this class with `AbstractWeapon` who contains some logic methods and implement the `WeaponInterface`. 


### Methods

Your new PHP class must implement some public methods :

- `getName(): string` : All weapons need a uniq name to identify it
- `getGridArray(): array` : This method return the matrix of shoot (0 => don't shoot; 1 => shoot)
- `getPrice(): integer` : Return the points you need to use this weapon
- `canAiUseIt(): boolean` (optional) : if a AI user can use this weapon. Don't give complex weapon to AI. (By default : false)
- `canBeRotate(): boolean` : If the matrix can be rotate
- `canBeShuffle(): boolean` : For animation, do a shuffle before shoot


### Register the weapon

- Register the class in `src/BonusBundle/Resources/config/services.yml`
- Don't forget the `weapon.type` tag.

### Enjoy
